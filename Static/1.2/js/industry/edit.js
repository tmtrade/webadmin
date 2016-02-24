/*
 *通栏添加、编辑js
 */
//添加分类
function addindustry() {
    var data = $("#addForm").serialize();
    if (data == "") {
        layer.msg('请填写添加内容');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/industry/addIndustry/',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    parent.location.reload();
                    //window.location.reload(true);
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}
// t=1 降、t=2升。
function setSort(t, s) {
    $.ajax({
        type: 'post',
        url: '/industry/setSort/',
        data: 's=' + s + '&t=' + t,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                var msg = data.msg == undefined ? '操作失败，请确认数据是否正确。' : data.msg;
                layer.msg(msg, {
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}
// t=1 降、t=2升。
function setClassSort(t, s, p) {
    $.ajax({
        type: 'post',
        url: '/tmclass/setClassSort/',
        data: 's=' + s + '&t=' + t + '&p=' + p,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                var msg = data.msg == undefined ? '操作失败，请确认数据是否正确。' : data.msg;
                layer.msg(msg, {
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}


function setPicSort(t, s) {
    $.ajax({
        type: 'post',
        url: '/industry/setPicSort/',
        data: 's=' + s + '&t=' + t,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                var msg = data.msg == undefined ? '操作失败，请确认数据是否正确。' : data.msg;
                layer.msg(msg, {
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

function setItemsSort(t, s) {
    $.ajax({
        type: 'post',
        url: '/industry/setItemsSort/',
        data: 's=' + s + '&t=' + t,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                var msg = data.msg == undefined ? '操作失败，请确认数据是否正确。' : data.msg;
                layer.msg(msg, {
                    time: 2000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

//添加分类 pic
function addindustryPic() {
    var data = $("#addPicForm").serialize();
    if (data == "") {
        layer.msg('请填写添加内容');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/industry/addIndustryPic/',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    parent.location.reload();
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

//删除 所有
function delAll(id) {
    if (id == "") {
        layer.msg('非法参数！');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/industry/del/',
        data: "id=" + id,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

//删除分类 pic
function delPic(pid) {
    if (pid == "") {
        layer.msg('非法参数！');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/industry/delIndustryPic/',
        data: "pid=" + pid,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

//删除子分类
function delItems(iid) {
    if (iid == "") {
        layer.msg('非法参数！');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/industry/delIndustryItems/',
        data: "iid=" + iid,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

//添加子分类
function addztype() {
    var data = $("#addztype").serialize();
    if (data == "") {
        layer.msg('请填写添加内容');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/industry/addZtype/',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    parent.location.reload();
                    //window.location.reload(true);
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}
//tmclass  移除标签
function removeLabel(id, label, t) {
    if (id == "") {
        layer.msg('非法参数！');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/tmclass/removeLabel/',
        data: "id=" + id + "&label=" + label,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    if (t == 1) {
                        window.location.reload();
                    } else {
                        parent.location.reload();
                    }

                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}
//tmclass  添加标签
function addLabel(t) {
    var id = $.trim($("#tcid").val());
    var label = $.trim($("#label").val());
    if (id == "") {
        layer.msg('非法参数！');
        return false;
    }
    if (label == "") {
        layer.msg('请填写添加标签！');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/tmclass/addLabel/',
        data: "id=" + id + "&label=" + label,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    if (t == 1) {
                        window.location.reload();
                    } else {
                        parent.location.reload();
                    }
                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}

//tmclass  添加标签
function editLabel(id, t) {
    var typeName = $.trim($("#typeName").val());
    if (id == "") {
        layer.msg('非法参数！');
        return false;
    }
    if (typeName == "") {
        layer.msg('标题不能为空！');
        return false;
    }
    $.ajax({
        type: 'post',
        url: '/tmclass/editTitle/',
        data: "id=" + id + "&typeName=" + typeName,
        dataType: 'json',
        success: function (data) {
            if (data.code == 1) {
                layer.msg('操作成功！', {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                }, function () {
                    if (t == 1) {
                        window.location.reload();
                    } else {
                        parent.location.reload();
                    }

                });
            } else {
                layer.msg(data.msg, {
                    time: 1000 //2秒关闭（如果不配置，默认是3秒）
                });
            }
        },
        error: function (data) {
            layer.msg('操作失败，请检查您输入的内容是否正确后重新尝试。', {
                time: 2000 //2秒关闭（如果不配置，默认是3秒）
            });
        }
    });
}
