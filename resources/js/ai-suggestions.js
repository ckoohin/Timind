class AISuggestions {
    constructor() {
        this.apiToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.baseUrl = '/api/ai';
        this.loading = false;
    }

    async getScheduleSuggestions(date = null) {
        if (this.loading) return;
        
        this.loading = true;
        this.showLoadingState('suggestions');
        
        try {
            const params = date ? `?date=${date}` : '';
            const response = await fetch(`${this.baseUrl}/schedule-suggestions${params}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                }
            });

            const goalForm = document.getElementById('goal-form');
            if (goalForm && !goalForm.dataset.aiBound) {
                goalForm.dataset.aiBound = "true";
                goalForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const subject = document.getElementById('subject').value;
                    const goal = document.getElementById('goal').value;
                    const workload = document.getElementById('workload').value;
                    const frequency = document.getElementById('frequency').value;

                    const startDay = document.getElementById('start-day').value;
                    const startMonth = document.getElementById('start-month').value;
                    const startYear = document.getElementById('start-year').value;

                    const endDay = document.getElementById('end-day').value;
                    const endMonth = document.getElementById('end-month').value;
                    const endYear = document.getElementById('end-year').value;

                    if (!startDay || !startMonth || !startYear || !endDay || !endMonth || !endYear) {
                        alert('Vui lòng nhập đầy đủ thông tin ngày tháng!');
                        return;
                    }

                    generateSchedule(subject, goal, workload, frequency, startDay, startMonth, startYear, endDay, endMonth, endYear);

                    document.getElementById('results-section').style.display = 'block';
                    document.getElementById('results-section').scrollIntoView({ behavior: 'smooth' });
                });

                function generateSchedule(subject, goal, workload, frequency, startDay, startMonth, startYear, endDay, endMonth, endYear) {
                    const resultsContainer = document.getElementById('schedule-results');

                    // Sample schedule generation
                    const scheduleHtml = `
                        <div class="result-card">
                            <div class="flex items-center mb-2">
                                <span class="text-blue-600 mr-2">📅</span>
                                <strong>Lịch học gợi ý:</strong>
                            </div>
                            <div class="ml-6 space-y-1 text-sm text-gray-700">
                                <div>${startDay}/${startMonth} - 13:30-15:15: ${subject} - Bài 1</div>
                                <div>${parseInt(startDay)+2}/${startMonth} - 13:30-15:15: ${subject} - Bài 2</div>
                                <div>${parseInt(startDay)+5}/${startMonth} - 13:30-15:15: ${subject} - Bài 3</div>
                            </div>
                        </div>

                        <div class="result-card success">
                            <div class="flex items-center mb-2">
                                <span class="text-green-600 mr-2">🌟</span>
                                <strong>Bạn nên nghỉ 5-10 phút sau mỗi 45 phút học</strong>
                            </div>
                            <p class="ml-6 text-sm text-gray-600">Điều này giúp não bộ xử lý thông tin tốt hơn và duy trì sự tập trung.</p>
                        </div>

                        <div class="result-card warning">
                            <div class="flex items-center mb-2">
                                <span class="text-yellow-600 mr-2">⚠️</span>
                                <strong>Lịch học tuần này của bạn đang quá dày</strong>
                            </div>
                            <p class="ml-6 text-sm text-gray-600">Khuyến nghị giảm ${frequency} xuống còn ${Math.max(1, parseInt(frequency)-1)} lần/tuần để đảm bảo chất lượng học tập.</p>
                        </div>

                        <div class="result-card">
                            <div class="flex items-center mb-2">
                                <span class="text-purple-600 mr-2">💡</span>
                                <strong>Gợi ý bổ sung:</strong>
                            </div>
                            <div class="ml-6 text-sm text-gray-700 space-y-1">
                                <div>• Chuẩn bị tài liệu trước 1 ngày</div>
                                <div>• Ôn tập lại kiến thức sau 24h</div>
                                <div>• Thực hành bài tập ${goal} mỗi tuần</div>
                            </div>
                        </div>
                    `;

                    resultsContainer.innerHTML = scheduleHtml;
                }

                const saveBtn = document.getElementById('save-schedule');
                if (saveBtn) {
                    saveBtn.addEventListener('click', function() {
                        alert('🎉 Lịch học đã được lưu thành công!\n\nBạn có thể xem lịch trong phần "Lịch của tôi".');
                    });
                }

                // Auto-focus next input for date fields
                document.querySelectorAll('.date-input').forEach((input, index, inputs) => {
                    input.addEventListener('input', function() {
                        if (this.value.length === this.maxLength && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    });
                });

                // Set default dates
                const today = new Date();
                const startDayInput = document.getElementById('start-day');
                const startMonthInput = document.getElementById('start-month');
                const startYearInput = document.getElementById('start-year');
                if (startDayInput && startMonthInput && startYearInput) {
                    startDayInput.value = String(today.getDate()).padStart(2, '0');
                    startMonthInput.value = String(today.getMonth() + 1).padStart(2, '0');
                    startYearInput.value = today.getFullYear();
                }
                const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
                const endDayInput = document.getElementById('end-day');
                const endMonthInput = document.getElementById('end-month');
                const endYearInput = document.getElementById('end-year');
                if (endDayInput && endMonthInput && endYearInput) {
                    endDayInput.value = String(nextMonth.getDate()).padStart(2, '0');
                    endMonthInput.value = String(nextMonth.getMonth() + 1).padStart(2, '0');
                    endYearInput.value = nextMonth.getFullYear();
                }
            }
            // --- END: Goal form and schedule generation logic ---
            
            const data = await response.json();
            
            if (data.success) {
                this.renderSuggestions(data.data.suggestions);
                this.showSuccessMessage('Đã tạo gợi ý lịch thành công!');
            } else {
                this.showError(data.message || 'Không thể tạo gợi ý lịch');
            }
            
        } catch (error) {
            console.error('Error getting suggestions:', error);
            this.showError('Lỗi kết nối API');
        } finally {
            this.loading = false;
            this.hideLoadingState('suggestions');
        }
    }

    async getGoalsAnalysis(timeRange = 7) {
        if (this.loading) return;
        
        this.loading = true;
        this.showLoadingState('analysis');
        
        try {
            const response = await fetch(`${this.baseUrl}/goals-analysis?time_range=${timeRange}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.renderAnalysis(data.data);
                this.showSuccessMessage('Đã phân tích dữ liệu thành công!');
            } else {
                this.showError(data.message || 'Không thể phân tích dữ liệu');
            }
            
        } catch (error) {
            console.error('Error getting analysis:', error);
            this.showError('Lỗi kết nối API');
        } finally {
            this.loading = false;
            this.hideLoadingState('analysis');
        }
    }

    async createActivityFromSuggestion(suggestion, date) {
        try {
            const response = await fetch(`${this.baseUrl}/create-from-suggestion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${this.getAuthToken()}`,
                    'X-CSRF-TOKEN': this.apiToken
                },
                body: JSON.stringify({
                    title: suggestion.title,
                    suggested_time: suggestion.suggested_time,
                    duration: suggestion.duration,
                    category: suggestion.category,
                    priority: suggestion.priority,
                    date: date
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccessMessage(`Đã thêm "${suggestion.title}" vào lịch!`);
                this.refreshCalendar();
                return true;
            } else {
                this.showError(data.message || 'Không thể tạo hoạt động');
                return false;
            }
            
        } catch (error) {
            console.error('Error creating activity:', error);
            this.showError('Lỗi kết nối API');
            return false;
        }
    }

    renderSuggestions(suggestions) {
        const container = document.getElementById('ai-suggestions-container');
        if (!container) return;

        if (!suggestions || suggestions.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-500 py-8">Không có gợi ý nào cho thời gian này</div>';
            return;
        }

        const html = suggestions.map(suggestion => `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-3 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">${suggestion.title}</h4>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                ${suggestion.suggested_time} (${suggestion.duration} phút)
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs ${this.getCategoryColor(suggestion.category)}">
                                ${this.getCategoryName(suggestion.category)}
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs ${this.getPriorityColor(suggestion.priority)}">
                                ${this.getPriorityName(suggestion.priority)}
                            </span>
                        </div>
                        <p class="text-sm text-gray-700 mt-2">${suggestion.reason}</p>
                    </div>
                    <button 
                        onclick="aiSuggestions.createActivityFromSuggestion(${JSON.stringify(suggestion).replace(/"/g, '&quot;')}, '${this.getCurrentDate()}')"
                        class="ml-4 px-4 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition-colors"
                    >
                        Thêm vào lịch
                    </button>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    renderAnalysis(data) {
        const container = document.getElementById('ai-analysis-container');
        if (!container) return;

        const { analysis, stats } = data;

        const html = `
            <div class="space-y-6">
                <!-- Tổng quan -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Phân tích tổng quan
                    </h3>
                    <p class="text-gray-700 leading-relaxed">${analysis.overall_analysis}</p>
                </div>

                <!-- Thống kê -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Tỷ lệ hoàn thành</p>
                                <p class="text-2xl font-bold text-green-900">${stats.completion_rate}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Tổng thời gian</p>
                                <p class="text-2xl font-bold text-blue-900">${stats.total_time_hours}h</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600">Hoạt động</p>
                                <p class="text-2xl font-bold text-purple-900">${stats.total_activities}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Điểm mạnh và yếu -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    ${analysis.strengths ? `
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h4 class="font-semibold text-green-600 mb-3">✅ Điểm mạnh</h4>
                        <ul class="space-y-2">
                            ${analysis.strengths.map(strength => `
                                <li class="flex items-start">
                                    <span class="text-green-500 mr-2">•</span>
                                    <span class="text-gray-700">${strength}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    ` : ''}

                    ${analysis.weaknesses ? `
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h4 class="font-semibold text-red-600 mb-3">⚠️ Cần cải thiện</h4>
                        <ul class="space-y-2">
                            ${analysis.weaknesses.map(weakness => `
                                <li class="flex items-start">
                                    <span class="text-red-500 mr-2">•</span>
                                    <span class="text-gray-700">${weakness}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    ` : ''}
                </div>

                <!-- Gợi ý cải thiện -->
                ${analysis.suggestions ? `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h4 class="font-semibold text-blue-600 mb-3">💡 Gợi ý cải thiện</h4>
                    <ul class="space-y-3">
                        ${analysis.suggestions.map(suggestion => `
                            <li class="flex items-start">
                                <span class="text-blue-500 mr-2">→</span>
                                <span class="text-gray-700">${suggestion}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
                ` : ''}

                <!-- Phân bố thời gian -->
                ${analysis.time_distribution ? `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">📊 Phân bố thời gian</h4>
                    <div class="space-y-3">
                        ${Object.entries(analysis.time_distribution).map(([category, percentage]) => `
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600 capitalize">${this.getCategoryName(category)}</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">${percentage}%</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : ''}
            </div>
        `;

        container.innerHTML = html;
    }

    /**
     * Utility functions
     */
    getCategoryColor(category) {
        const colors = {
            work: 'bg-blue-100 text-blue-800',
            personal: 'bg-green-100 text-green-800',
            exercise: 'bg-red-100 text-red-800',
            learning: 'bg-purple-100 text-purple-800',
            rest: 'bg-gray-100 text-gray-800'
        };
        return colors[category] || 'bg-gray-100 text-gray-800';
    }

    getCategoryName(category) {
        const names = {
            work: 'Công việc',
            personal: 'Cá nhân',
            exercise: 'Thể thao',
            learning: 'Học tập',
            rest: 'Nghỉ ngơi'
        };
        return names[category] || category;
    }

    getPriorityColor(priority) {
        const colors = {
            high: 'bg-red-100 text-red-800',
            medium: 'bg-yellow-100 text-yellow-800',
            low: 'bg-green-100 text-green-800'
        };
        return colors[priority] || 'bg-gray-100 text-gray-800';
    }

    getPriorityName(priority) {
        const names = {
            high: 'Cao',
            medium: 'Trung bình',
            low: 'Thấp'
        };
        return names[priority] || priority;
    }

    getCurrentDate() {
        return new Date().toISOString().split('T')[0];
    }

    getAuthToken() {
        return localStorage.getItem('auth_token') || 
               document.querySelector('meta[name="api-token"]')?.getAttribute('content');
    }

    showLoadingState(type) {
        const container = document.getElementById(type === 'suggestions' ? 'ai-suggestions-container' : 'ai-analysis-container');
        if (container) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="ml-3 text-gray-600">Đang xử lý với AI...</span>
                </div>
            `;
        }
    }

    hideLoadingState(type) {
        // Loading state sẽ được thay thế bởi nội dung thực tế
    }

    showSuccessMessage(message) {
        this.showToast(message, 'success');
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    refreshCalendar() {
        window.dispatchEvent(new CustomEvent('calendar-refresh'));
        
        if (typeof window.refreshCalendar === 'function') {
            window.refreshCalendar();
        }
    }
}

const aiSuggestions = new AISuggestions();

document.addEventListener('DOMContentLoaded', function() {
    const suggestionsBtn = document.getElementById('get-ai-suggestions');
    if (suggestionsBtn) {
        suggestionsBtn.addEventListener('click', function() {
            const dateInput = document.getElementById('suggestion-date');
            const selectedDate = dateInput ? dateInput.value : null;
            aiSuggestions.getScheduleSuggestions(selectedDate);
        });
    }

    const analysisBtn = document.getElementById('get-ai-analysis');
    if (analysisBtn) {
        analysisBtn.addEventListener('click', function() {
            const timeRangeSelect = document.getElementById('analysis-time-range');
            const timeRange = timeRangeSelect ? timeRangeSelect.value : 7;
            aiSuggestions.getGoalsAnalysis(timeRange);
        });
    }
});

window.aiSuggestions = aiSuggestions;