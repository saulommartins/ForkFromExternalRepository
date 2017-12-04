<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Gerar o componente composto por um combo e suas opções de periodicidade
    * Data de Criação: 01/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package framework
    * @subpackage componentes

    $Id: Periodicidade.class.php 63968 2015-11-12 18:00:32Z jean $

    Casos de uso: uc-01.01.00

*/

/**
    * Gerar o componente composto por um combo e suas opções de periodicidade
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package framework
    * @subpackage componentes
*/
class Periodicidade extends Componente
{
/**
    * @access Private
    * @var String
*/
var $stExercicio;

/**
    * @access Private
    * @var String
*/
var $stIdComponente;
/**
    * @access Private
    * @var Boolean
*/
var $boValidaExercicio;
/**
    * @access Private
    * @var Object
*/
var $obPeriodicidade;
/**
    * @access Private
    * @var Object
*/
var $obDia;
/**
    * @access Private
    * @var Object
*/
var $obMes;
/**
    * @access Private
    * @var Object
*/
var $obAnoMes;
/**
    * @access Private
    * @var Object
*/
var $obHdnAnoMes;
/**
    * @access Private
    * @var Object
*/
var $obLblAnoMes;
/**
    * @access Private
    * @var Object
*/
var $obAno;
/**
    * @access Private
    * @var Object
*/
var $obPeriodoInicial;
/**
    * @access Private
    * @var Object
*/
var $obPeriodoFinal;
/**
    * @access Private
    * @var Object
*/
var $obPeriodoLabel;
/**
    * @access Private
    * @var Object
*/
var $obSpan;
/**
    * @access Private
    * @var Object
*/
var $obDataInicial;
/**
    * @access Private
    * @var Object
*/
var $obDataFinal;
/**
    * @access Private
    * @var Object
*/
var $boExibeDia;

/**
    * @access Private
    * @var Object
*/
var $boAnoVazio;

/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setIdComponente($valor) { $this->stIdComponente = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setValidaExercicio($valor) { $this->boValidaExercicio = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPeriodicidade($valor) { $this->obPeriodicidade   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDia($valor) { $this->obDia   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMes($valor) { $this->obMes   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAnoMes($valor) { $this->obAnoMes   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setHdnAnoMes($valor) { $this->obHdnAnoMes   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setLblAnoMes($valor) { $this->obLblAnoMes   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAno($valor) { $this->obAno   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPeriodoInicial($valor) { $this->obPeriodoInicial   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPeriodoFinal($valor) { $this->obPeriodoFinal   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setPeriodoLabel($valor) { $this->obPeriodoLabel   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setSpan($valor) { $this->obSpan   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDataInicial($valor) { $this->obDataInicial   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setDataFinal($valor) { $this->obDataFinal   = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setExibeDia($valor) { $this->boExibeDia = $valor; }

/**
    * @access Public
    * @return String
*/

function setAnoVazio($valor) { $this->boAnoVazio = $valor; }

/**
    * @access Public
    * @return Boolean
*/

function getExercicio() { return $this->stExercicio; }
/**
    * @access Public
    * @return String
*/
function getIdComponente() { return $this->stIdComponente; }

/**
    * @access Public
    * @return Boolean
*/
function getValidaExercicio() { return $this->boValidaExercicio; }
/**
    * @access Public
    * @return Object
*/
function getPeriodicidade() { return $this->obPeriodicidade; }
/**
    * @access Public
    * @return Object
*/
function getDia() { return $this->obDia;   }
/**
    * @access Public
    * @return Object
*/
function getMes() { return $this->obMes;   }
/**
    * @access Public
    * @return Object
*/
function getAnoMes() { return $this->obAnoMes;   }
/**
    * @access Public
    * @return Object
*/
function getHdnAnoMes() { return $this->obHdnAnoMes;   }
/**
    * @access Public
    * @return Object
*/
function getLblAnoMes() { return $this->obLblAnoMes;   }
/**
    * @access Public
    * @return Object
*/
function getAno() { return $this->obAno;   }
/**
    * @access Public
    * @return Object
*/
function getPeriodoInicial() { return $this->obPeriodoInicial;   }
/**
    * @access Public
    * @return Object
*/
function getPeriodoFinal() { return $this->obPeriodoFinal;   }
/**
    * @access Public
    * @return Object
*/
function getPeriodoLabel() { return $this->obPeriodoLabel;   }
/**
    * @access Public
    * @return Object
*/
function getSpan() { return $this->obSpan;   }
/**
    * @access Public
    * @return Object
*/
function getDataInicial() { return $this->obDataInicial;   }
/**
    * @access Public
    * @return Object
*/
function getDataFinal() { return $this->obDataFinal;   }
/**
    * @access Public
    * @return Object
*/
function getExibeDia() { return $this->boExibeDia;  }
/**
    * Método Construtor
    * @access Public
*/

function getAnoVazio() { return $this->boAnoVazio;  }
/**
    * Método Construtor
    * @access Public
*/

function Periodicidade()
{
    parent::Componente();
    $this->setDefinicao                 ( "PERIODICIDADE"     );
    $this->setExibeDia( true  );
    if(!$this->getRotulo())
        $this->setRotulo("Periodicidade");

    if(!$this->getTiTle())
        $this->setTitle( "Selecione a Periodicidade."  );

    $this->setPeriodicidade             ( new Select        );
    $this->obPeriodicidade->setName     ("inPeriodicidade".$this->getIdComponente() );
    $this->obPeriodicidade->setId       ("inPeriodicidade".$this->getIdComponente() );
    $this->obPeriodicidade->setRotulo   ( "Periodicidade"   );
    $this->obPeriodicidade->setStyle    ( "width: 100px"    );

    $this->setDia                       ( new Data          );
    $this->obDia->setName               ("stDia".$this->getIdComponente() );
    $this->obDia->setId                 ("stDia".$this->getIdComponente() );
    $this->obDia->setRotulo             ( "Dia"             );

    $this->setMes                       ( new Select        );
    $this->obMes->setName               ("stMes".$this->getIdComponente() );
    $this->obMes->setId                 ("stMes".$this->getIdComponente() );
    $this->obMes->setRotulo             ( "Mes"             );
    $this->obMes->setValue              ( ""                );
    $this->obMes->addOption             ( "","Selecione"    );
    $this->obMes->addOption             ( "01", "Janeiro"   );
    $this->obMes->addOption             ( "02", "Fevereiro" );
    $this->obMes->addOption             ( "03", "Março"     );
    $this->obMes->addOption             ( "04", "Abril"     );
    $this->obMes->addOption             ( "05", "Maio"      );
    $this->obMes->addOption             ( "06", "Junho"     );
    $this->obMes->addOption             ( "07", "Julho"     );
    $this->obMes->addOption             ( "08", "Agosto"    );
    $this->obMes->addOption             ( "09", "Setembro"  );
    $this->obMes->addOption             ( "10", "Outubro"   );
    $this->obMes->addOption             ( "11", "Novembro"  );
    $this->obMes->addOption             ( "12", "Dezembro"  );

    $this->setHdnAnoMes ( new Hidden);
    $this->obHdnAnoMes->setName ( "stAnoMes".$this->getIdComponente() );
    $this->obHdnAnoMes->setId   ( "stHdnAnoMes".$this->getIdComponente() );
    $this->obHdnAnoMes->setValue( ""  );

    $this->setLblAnoMes ( new Label);
    $this->obLblAnoMes->setRotulo( "AnoMes"        );
    $this->obLblAnoMes->setId    ( "inAnoMes".$this->getIdComponente() );

    $this->setAnoMes                       ( new TextBox       );
    $this->obAnoMes->setName               ("stAnoMes".$this->getIdComponente() );
    $this->obAnoMes->setId                 ("stAnoMes".$this->getIdComponente() );
    $this->obAnoMes->setRotulo             ( "AnoMes"          );
    $this->obAnoMes->setSize               ( 4                 );
    $this->obAnoMes->setMaxLength          ( 4                 );
    $this->obAnoMes->setTitle              ( "Informe o ano"   );
    $this->obAnoMes->setInteiro            ( true              );

    $this->setAno                       ( new TextBox       );
    $this->obAno->setName               ("stAno".$this->getIdComponente() );
    $this->obAno->setId                 ("stAno".$this->getIdComponente() );
    $this->obAno->setRotulo             ( "Ano"             );
    $this->obAno->setSize               ( 4                 );
    $this->obAno->setMaxLength          ( 4                 );
    $this->obAno->setTitle              ( "Informe o ano"   );
    $this->obAno->setInteiro            ( true              );

    $this->setPeriodoInicial            ( new Data          );
    $this->obPeriodoInicial->setName    ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoInicial->setId      ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoInicial->setRotulo  ( "Intervalo"         );

    $this->setPeriodoFinal              ( new Data          );
    $this->obPeriodoFinal->setName      ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoFinal->setId        ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoFinal->setRotulo    ( "Intervalo"         );

    $this->setPeriodoLabel              ( new Label         );
    $this->obPeriodoLabel->setValue     ( " até "           );

    $this->setSpan                      ( new Span          );
    $this->obSpan->setId                ("spanPeriodicidade".$this->getIdComponente() );

    $this->setDataInicial               ( new Hidden        );
    $this->obDataInicial->setName       ('stDataInicial'.$this->getIdComponente() );
    $this->obDataInicial->setId         ('stDataInicial'.$this->getIdComponente() );

    $this->setDataFinal                 ( new Hidden        );
    $this->obDataFinal->setName         ('stDataFinal'.$this->getIdComponente() );
    $this->obDataFinal->setId           ('stDataFinal'.$this->getIdComponente() );

    $this->setAnoVazio                  ( false );

}

/**
    * Monta o HTML do Objeto Periodicidade
    * @access Protected
*/
function montaHtml()
{
    //adicionado para poder existir mais de uma instância deste componente
    $this->obPeriodicidade->setName     ("inPeriodicidade".$this->getIdComponente() );
    $this->obPeriodicidade->setId       ("inPeriodicidade".$this->getIdComponente() );
    $this->obPeriodoInicial->setName    ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoInicial->obEvento->setOnChange ("preenchePeriodoInicial".$this->getIdComponente()."(this.value)");
    $this->obPeriodoFinal->setName      ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoFinal->obEvento->setOnChange ("preenchePeriodoFinal".$this->getIdComponente()."(this.value)");
    $this->obSpan->setId                ("spanPeriodicidade".$this->getIdComponente() );
    $this->obDataInicial->setName       ($this->obDataInicial->getName().$this->getIdComponente() );
    $this->obDataFinal->setName         ($this->obDataFinal->getName().$this->getIdComponente() );

    $this->obDia->setName               ("stDia".$this->getIdComponente() );
    $this->obDia->setId                 ("stDia".$this->getIdComponente() );
    $this->obDia->obEvento->setOnChange ("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&".$this->obDia->getName()."='+this.value,'preencheDia');");

    $this->obAnoMes->setName               ("stAnoMes".$this->getIdComponente() );
    $this->obAnoMes->setId                 ("stAnoMes".$this->getIdComponente() );
    $this->obAnoMes->obEvento->setOnBlur   ("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&".$this->obMes->getName().$this->getIdComponente()."='+document.frm.".$this->obMes->getName().$this->getIdComponente().".value+'&".$this->obAnoMes->getName()."='+this.value,'preencheMes');");

    $this->obMes->setName               ("stMes".$this->getIdComponente() );
    $this->obMes->setId                 ("stMes".$this->getIdComponente() );
    $this->obMes->obEvento->setOnChange ("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&".$this->obMes->getName()."='+this.value+'&".$this->obAnoMes->getName()."='+document.frm.".$this->obAnoMes->getName().".value,'preencheMes');");

    $this->setHdnAnoMes ( new Hidden);
    $this->obHdnAnoMes->setName ( "stAnoMes".$this->getIdComponente() );
    $this->obLblAnoMes->setId    ( "inAnoMes".$this->getIdComponente() );

    $this->obAno->setName               ("stAno".$this->getIdComponente() );
    $this->obAno->setId                 ("stAno".$this->getIdComponente() );
    $this->obAno->obEvento->setOnChange ("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&".$this->obAno->getName()."='+this.value,'preencheAno');");

    $this->obPeriodoInicial->setName    ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoInicial->setId      ("stPeriodoInicial".$this->getIdComponente() );
    $this->obPeriodoInicial->obEvento->setOnChange("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&".$this->obPeriodoInicial->getName()."='+this.value+'&stTipo=inicial','preenchePeriodo');");

    $this->obPeriodoFinal->setName      ("stPeriodoFinal".$this->getIdComponente() );
    $this->obPeriodoFinal->setId        ("stPeriodoFinal".$this->getIdComponente() );
    $this->obPeriodoFinal->obEvento->setOnChange("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&".$this->obPeriodoFinal->getName()."='+this.value+'&stTipo=final','preenchePeriodo');");

    if ( $this->getExibeDia() ) {
        $this->obPeriodicidade->addOption   ( 1, "Dia"          );
    }
    $this->obPeriodicidade->addOption   ( 2, "Mês"          );
    $this->obPeriodicidade->addOption   ( 3, "Ano"          );
    $this->obPeriodicidade->addOption   ( 4, "Intervalo"    );

    if (!$this->obDataInicial->getName()) {
        $this->obDataInicial->setName       ($this->obDataInicial->getName().$this->getIdComponente() );
        $this->obDataInicial->setId         ($this->obDataInicial->getName().$this->getIdComponente() );
    }

    if (!$this->obDataFinal->getName()) {
        $this->obDataFinal->setName         ($this->obDataFinal->getName().$this->getIdComponente() );
        $this->obDataFinal->setId           ($this->obDataFinal->getName().$this->getIdComponente() );
    }

    //MONTA O OPÇÃO "PERIODO"
    $this->obPeriodoInicial->montaHTML();
    $stHTML = $this->obPeriodoInicial->getHTML();

    $this->obPeriodoLabel->montaHTML();
    $stHTML .= $this->obPeriodoLabel->getHTML();

    $this->obPeriodoFinal->montaHTML();
    $stHTML .= $this->obPeriodoFinal->getHTML();

    if ($this->getValue()==4 or !$this->getValue()) {
        $this->obSpan->setValue($stHTML);
    }

    //MONTA A PERIODICIDADE
    $this->obPeriodicidade->setName( $this->obPeriodicidade->getName() );
    $this->obPeriodicidade->setId  ( $this->obPeriodicidade->getName() );
    $this->obPeriodicidade->obEvento->setOnChange("ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$this->getIdComponente()."&inCodPeriodo='+this.value,'montaSpan');" );

    if($this->getValue())
        $this->obPeriodicidade->setValue ($this->getValue());
    else
        $this->obPeriodicidade->setValue (4);

    $this->obPeriodicidade->montaHTML();
    $stHtml = $this->obPeriodicidade->getHTML() . "&nbsp;&nbsp;";

    $this->obSpan->montaHTML();
    $stHtml .= $this->obSpan->getHTML();

    $this->obDataInicial->montaHTML();
    $stHtml .= $this->obDataInicial->getHTML();

    $this->obDataFinal->montaHTML();
    $stHtml .= $this->obDataFinal->getHTML();

    $this->setHtml($stHtml);

    #sessao->componentes['obPeriodicidade'.$this->getIdComponente()] = serialize( $this );
    #_SESSION['sessao'] = $sessao;

    Sessao::write('obPeriodicidade'.$this->getIdComponente(), $this);

}
}

?>
