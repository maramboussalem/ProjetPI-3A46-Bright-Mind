#ifndef C_H
#define C_H

#include <QDeclarativeItem>
#include <QMainWindow>
#include <QObject>
#include <QQuickItem>
#include <QSharedDataPointer>
#include <QWidget>

class cData;

class c
{
    Q_OBJECT
public:
    c();
    c(const c &);
    c &operator=(const c &);
    ~c();

private:
    QSharedDataPointer<cData> data;
};

#endif // C_H
