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
* Classe de regra de interface para Competência
* Data de Criação: 22/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package framework
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                );
include_once( CAM_GRH_PES_COMPONENTES."SeletorAno.class.php"                                    );

class IFiltroCompetencia extends Objeto
{
    public $obRFolhaPagamentoPeriodoMovimentacao;
    public $obCmbMes;
    public $obSeletorAno;
    public $obTxtAno;
    public $boCompetenciaAtual;
    public $dtCompetenciaAtual;
    public $boCompetenciaAnteriores;
    public $stComplemento;
    public $stRotulo;
    public $boDisabledSession;

    public function setRFolhaPagamentoPeriodoMovimentacao($valor)
    {
        $this->obRFolhaPagamentoPeriodoMovimentacao = $valor;
    }

    public function setCompetenciaAtual($valor)
    {
        $this->boCompetenciaAtual = $valor;
    }

    public function setDtCompetenciaAtual($valor)
    {
        $this->dtCompetenciaAtual = $valor;
    }

    public function setCompetenciaAnteriores($valor)
    {
        $this->boCompetenciaAnteriores = $valor;
    }

    public function setTextAno($valor)
    {
        $this->obTxtAno = $valor;
    }
    public function setComplemento($valor)
    {
        $this->stComplemento = $valor;
    }
    public function setRotulo($valor)
    {
        $this->stRotulo = $valor;
    }

    public function setDisabledSession($valor)
    {
        $this->boDisabledSession = $valor;
    }

    public function setCodigoPeriodoMovimentacao($valor)
    {
        $this->inCodigoPeriodoMovimentacao = $valor;
    }

    public function getRFolhaPagamentoPeriodoMovimentacao()
    {
        return $this->obRFolhaPagamentoPeriodoMovimentacao;
    }

    public function getCompetenciaAtual()
    {
        return $this->boCompetenciaAtual;
    }

    public function getDtCompetenciaAtual()
    {
        return $this->dtCompetenciaAtual;
    }

    public function getCompetenciaAnteriores()
    {
        return $this->boCompetenciaAnteriores;
    }

    public function getComplemento()
    {
        return $this->stComplemento;
    }

    public function getRotulo()
    {
        return $this->stRotulo;
    }

    public function getDisabledSession()
    {
        return $this->boDisabledSession;
    }

    public function getCodigoPeriodoMovimentacao()
    {
        return $this->inCodigoPeriodoMovimentacao;
    }

    public function buscaCodigoPeriodoMovimentacao($inAno, $inMes)
    {
        if ((int) $inAno > 0 && (int) $inMes > 0) {
            $this->obRFolhaPagamentoPeriodoMovimentacao->obTFolhaPagamentoPeriodoMovimentacao->setDado('ano', (int) $inAno);
            $this->obRFolhaPagamentoPeriodoMovimentacao->obTFolhaPagamentoPeriodoMovimentacao->setDado('mes', (int) $inMes);
            $this->obRFolhaPagamentoPeriodoMovimentacao->obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

            if ($rsPeriodoMovimentacao->getNumLinhas() > 0) {
               $this->setCodigoPeriodoMovimentacao($rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));

               return true;
            }
        }

        return false;
    }

    public function IFiltroCompetencia($boCompetenciaAtual=true,$dtCompetenciaAtual="",$boCompetenciaAnteriores=true)
    {
        Sessao::remove("arSelectMultiploLotacao");
        Sessao::remove("arFiltroTipoFolha");

        $this->setRFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
        $this->setRotulo("Competência");

        if ($dtCompetenciaAtual != "") {
            $arData = explode("/",$dtCompetenciaAtual);
        } else {
            $this->obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);
            $dtCompetenciaAtual = $rsUltimaMovimentacao->getCampo("dt_final");
            $arData = explode("/",$rsUltimaMovimentacao->getCampo("dt_final"));
        }

        $this->setCompetenciaAtual($boCompetenciaAtual);
        $this->setDtCompetenciaAtual($dtCompetenciaAtual);
        $this->setCompetenciaAnteriores($boCompetenciaAnteriores);
        $this->setDisabledSession(false);

        $inCodMes = $arData[1];
        $inAno    = $arData[2];

        $arMeses = array("1"=>"Janeiro"  ,
                         "2"=>"Fevereiro",
                         "3"=>"Março"    ,
                         "4"=>"Abril"    ,
                         "5"=>"Maio"     ,
                         "6"=>"Junho"    ,
                         "7"=>"Julho"    ,
                         "8"=>"Agosto"   ,
                         "9"=>"Setembro" ,
                         "10"=>"Outubro" ,
                         "11"=>"Novembro",
                         "12"=>"Dezembro");

        $this->obSeletorAno = new SeletorAno;
        $this->obSeletorAno->setAnoInicial         ( $inAno                                    );
        $this->obSeletorAno->setValue              ( $inAno                                    );
        $this->obSeletorAno->setTitle              ( "Informe a competência."                  );
        $this->obSeletorAno->obEvento->setOnKeyUp  ("mascaraDinamico('2059', this, event);"    );
        $this->obSeletorAno->setNull               ( false                                     );

        $this->obCmbMes = new Select;
        $this->obCmbMes->setTitle                 ( "Informe a competência."                  );
        $this->obCmbMes->setValue                 ( $inCodMes                                 );
        $this->obCmbMes->setNull                  ( false                                     );
        $this->obCmbMes->setStyle                 ( "width: 200px"                            );
        $this->obCmbMes->addOption                ( "", "Selecione"                           );

        foreach ($arMeses as $stOption => $stValue) {
            if ($boCompetenciaAtual && !$boCompetenciaAnteriores) {
                if ($stOption >= $inCodMes) {
                    $this->obCmbMes->addOption("$stOption","$stValue");
                }
            } elseif ($boCompetenciaAtual && $boCompetenciaAnteriores) {
                if ($stOption <= $inCodMes) {
                    $this->obCmbMes->addOption("$stOption","$stValue");
                }
            } else {
                $this->obCmbMes->addOption("$stOption","$stValue");
            }
        }
        $this->obTxtAno = &$this->obSeletorAno->obTxtAno;
    }

    public function geraFormulario(&$obFormulario)
    {
        $this->buscaCodigoPeriodoMovimentacao($this->obSeletorAno->getValue(),
                                              $this->obCmbMes->getValue());

        $this->obSeletorAno->setRotulo($this->getRotulo());
        $this->obSeletorAno->setName( "inAno".$this->getComplemento() );
        $this->obSeletorAno->setId( "inAno".$this->getComplemento() );
        $this->obSeletorAno->obTxtAno->setName( "inAno".$this->getComplemento() );
        $this->obSeletorAno->obTxtAno->setId( "inAno".$this->getComplemento() );
        $this->obCmbMes->setRotulo($this->getRotulo());
        $this->obCmbMes->setName( "inCodMes".$this->getComplemento() );
        $this->obCmbMes->setId( "inCodMes".$this->getComplemento() );

        $stParametros  = "&stNomeComponente=".$this->obSeletorAno->getName();
        $stParametros .= "&inCodMes".$this->getComplemento()."='+document.frm.inCodMes".$this->getComplemento().".value+'";
        $stParametros .= "&inAno".$this->getComplemento()."='+document.frm.inAno".$this->getComplemento().".value+'";
        $stParametros .= "&stCompetenciaComplemento=".$this->getComplemento();
        $stParametros .= "&boPreencherCompetencia=true";
        $stParametros .= "&dtCompetenciaAtual=".$this->getDtCompetenciaAtual();
        $stParametros .= "&boCompetenciaAnteriores=".$this->getCompetenciaAnteriores();

        $stOnChangeMes = " ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroCompetencia.php?".Sessao::getId()."".$stParametros."','processarCompetenciaMes' );";
        $stOnChangeAno = " ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroCompetencia.php?".Sessao::getId()."".$stParametros."','processarCompetenciaAno' );";

        $stOnChangeCmbMes = $this->obCmbMes->obEvento->getOnChange();
        $stOnChangeTxtAno = $this->obTxtAno->obEvento->getOnChange();

        $this->obCmbMes->obEvento->setOnChange($stOnChangeMes.$stOnChangeCmbMes);
        $this->obTxtAno->obEvento->setOnChange($stOnChangeAno.$stOnChangeTxtAno);

        $stOnChangeTxtAno = $this->obTxtAno->obEvento->getOnChange();
        $this->obSeletorAno->obBtnCima->obEvento->setOnClick($stOnChangeTxtAno);
        $this->obSeletorAno->obBtnBaixo->obEvento->setOnClick($stOnChangeTxtAno);

        $this->obSeletorAno->montaHTML();
        $this->obCmbMes->montaHTML();

        $stHTML  = "<table>                                              \n";
        $stHTML .= "    <tr>                                             \n";
        $stHTML .= "        <td>                                         \n";
        $stHTML .= "              ".$this->obSeletorAno->getHTML()."     \n";
        $stHTML .= "        </td>                                        \n";
        $stHTML .= "        <td>                                         \n";
        $stHTML .= "               ".$this->obCmbMes->getHTML()."        \n";
        $stHTML .= "        </td>                                        \n";
        $stHTML .= "    </tr>                                            \n";
        $stHTML .= "</table>                                             \n";

        // Criando formulário
        $obForm = new Formulario;
        $obForm->addComponente($this->obSeletorAno);
        $obForm->addComponente($this->obCmbMes);

        // Adicionando validações javaScript dos campos no formulário
        $obForm->obJavaScript->montaJavaScript();
        $stEval = $obForm->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $stEval = str_replace("\"","'",$stEval);

        $obLabel = new Label;
        $obLabel->setRotulo ( $this->getRotulo() );
        $obLabel->setValue  ( $stHTML );

        $obHdnCompetencia = new hiddenEval();
        $obHdnCompetencia->setId("hdnCompetencia".$this->getComplemento());
        $obHdnCompetencia->setName("hdnCompetencia".$this->getComplemento());
        $obHdnCompetencia->setValue($stEval);

        $obFormulario->addComponente($obLabel);
        $obFormulario->addHidden($obHdnCompetencia, true);

        /*********************************************************************
        *  Carrega o array de refêrencia para o componente de lotação
        *  de acordo com o periodo de movimentação selecionado
        *  CUIDADO: Quando existir mais de um componente IFiltroCompetencia
        *           na tela só um pode estar habilitado
        * *******************************************************************/
        if (!$this->getDisabledSession()) {
            $arFiltroCompetencia = array($this);
            Sessao::write("arFiltroCompetencia", $arFiltroCompetencia);
        }
    }

}
?>
