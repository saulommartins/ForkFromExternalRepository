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
    * Gerar código JavaScript a partir dos componentes criados definidos usuário
    * Data de Criação: 11/02/2003

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-01.01.00

    $Id: JavaScript.class.php 66615 2016-10-04 19:38:37Z carlos.silva $

*/

/**
    * Classe que gera JavaScript dos componentes

    * @package framework
    * @subpackage componentes
*/
class JavaScript extends Objeto
{
/**
    * @access Private
    * @var Array
*/
var $arComponente;

/**
    * @access Private
    * @var String
*/
var $stForm;

/**
    * @access Private
    * @var Array
*/
var $arFuncao;

/**
    * @access Private
    * @var String
*/
var $stJavaScript;

/**
    * @access Private
    * @var String
*/
var $stSalvar;

/**
    * @access Private
    * @var String
*/
var $stValida;

/**
    * @access Private
    * @var String
*/
var $stComplementoValida;

/**
    * @access Private
    * @var String
*/
var $stMonitoraBuscaINNER;

/**
    * @access Private
    * @var String
*/
var $stLimpar;

/**
    * @access Private
    * @var String
*/
var $stInnerJavaScript;

/**
    * @access Private
    * @var Array
*/
var $arLayers;
var $stName;

//SETTERS
/**
    * @access Public
    * @param Array $Valor
*/
function setComponente($valor) { $this->arComponente      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setForm($valor) { $this->stForm            = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setFuncao($valor) { $this->arFuncao          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setJavaScript($valor) { $this->stJavaScript      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setSalvar($valor) { $this->stSalvar          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setValida($valor) { $this->stValida          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setComplementoValida($valor) { $this->stComplementoValida = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setMonitoraBuscaINNER($valor) { $this->stMonitoraBuscaINNER = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setLimpar($valor) { $this->stLimpar          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setInnerJavaScript($valor) { $this->stInnerJavaScript = $valor; }

/**
    * @access Public
    * @param Array $Valor
*/
function setLayers($valor) { $this->arLayers          = $valor; }

//GETTERS
/**
    * @access Public
    * @return Array
*/
function getComponente() { return $this->arComponente;      }

/**
    * @access Public
    * @return String
*/
function getForm() { return $this->stForm;            }

/**
    * @access Public
    * @return String
*/
function getFuncao() { return $this->arFuncao;          }

/**
    * @access Public
    * @return String
*/
function getJavaScript() { return $this->stJavaScript;      }

/**
    * @access Public
    * @return String
*/
function getSalvar() { return $this->stSalvar;          }

/**
    * @access Public
    * @return String
*/
function getValida() { return $this->stValida;          }

/**
    * @access Public
    * @return String
*/
function getComplementoValida() { return $this->stComplementoValida; }

/**
    * @access Public
    * @return String
*/
function getMonitoraBuscaINNER() { return $this->stMonitoraBuscaINNER; }

/**
    * @access Public
    * @return String
*/
function getLimpar() { return $this->stLimpar;          }

/**
    * @access Public
    * @return String
*/
function getInnerJavaScript() { return $this->stInnerJavaScript; }

/**
    * @access Public
    * @return Array
*/
function getLayers() { return $this->arLayers;          }

/**
    * Método construtor
    * @access Public
*/
function JavaScript($stName='')
{
    $this->stName   = $stName;
    $this->setForm("frm");
    $arComponente   = array();
    $this->setComponente( $arComponente );
    $arFuncao       = array();
    $this->setFuncao( $arFuncao );
    $stValida  = " function Valida() {\n";
    $stValida .= "     var retorno = true;\n";
    $stValida .= "     return retorno;\n";
    $stValida .= " }\n";
    $this->setValida( $stValida );
    $stSalvar  = " function Salvar() {\n";
    $stSalvar .= "     if ( Valida() ) {\n";
    $stSalvar .= "          document.".$this->getForm().".submit();\n";
    $stSalvar .= "     } else { LiberaFrames(true,true); }\n";
//    $stSalvar .= " }\n";
    $this->setSalvar( $stSalvar );
    $stLimpar  = " function limpaFormulario$stName() {\n";
    $stLimpar .= "      var retorno = true;\n";
    $stLimpar .= "      return retorno;\n";
    $stLimpar .= " }\n";
    $this->setLimpar( $stLimpar );
    $this->arLayers = array();
}

//METODOS DA CLASSE
/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obCompoente
*/
function addComponente($obComponente)
{
    $arComponente = $this->getComponente();
    $arComponente[] = $obComponente;
    $this->setComponente( $arComponente );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stFuncao
*/
function addFuncao($stFuncao)
{
    $arFuncao = $this->getFuncao();
    $arFuncao[] = $stFuncao;
    $this->setFuncao( $arFuncao );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obForm
*/
function addForm($obForm)
{
    $this->setForm( $obForm->getName() );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stLayer
*/
function addLayer($stLayer)
{
    $this->arLayers[] = $stLayer;
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @return String
*/
function geraHabilitaLayer()
{
    $stJavaScript = "";
    $stListaLayer = "";

    $stJavaScript .= "\n";
    $stJavaScript .= "function HabilitaLayer(stLayer) {\n";
    foreach ($this->arLayers as $stLayer) {
        $stListaLayer .= "'".$stLayer."',";
    }
    $stListaLayer = substr( $stListaLayer, 0, strlen( $stListaLayer ) - 1 );
    $stJavaScript .= "    var arLayer = new Array(".$stListaLayer.");\n";
    $stJavaScript .= "    var stAba;\n";
    $stJavaScript .= "    for (var i = 0; i < arLayer.length ; i++) {\n";
    $stJavaScript .= "        if (arLayer[i] == stLayer) {\n";
    $stJavaScript .= "            document.getElementById(stLayer).style.display = 'block';\n";
    $stJavaScript .= "            stAba = eval( document.getElementById('celula_' + (i + 1) ) );\n";
    $stJavaScript .= "            stAba.className               = 'show_dados_center_aba';\n";
    $stJavaScript .= "        } else {\n";
    $stJavaScript .= "            document.getElementById(arLayer[i]).style.display = 'none';\n";
    $stJavaScript .= "            stAba = eval( document.getElementById('celula_' + (i + 1) ) );\n";
    $stJavaScript .= "            stAba.className               = 'labelcenter_aba';\n";
    $stJavaScript .= "        }\n";
    $stJavaScript .= "        document.getElementById('BOTAO').style.display = 'none'; \n";
    $stJavaScript .= "        document.getElementById('BOTAO').style.display = 'block'; \n";
    $stJavaScript .= "    }\n";
    $stJavaScript .= "}\n";

    return $stJavaScript;
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function montaJavaScript()
{
    $arComponente = $this->getComponente();
    $stJavaScript = "";
    $stValida = "";
    $stLimpar = "";
    $arMonitoraBuscaINNER = array();
    if ( count( $arComponente ) ) {
        //foreach ($arComponente as $obComponente) {
        for ( $inCont = 0; $inCont < count( $arComponente ); $inCont++ ) {
            $obComponente = $arComponente[$inCont];
            $stDefinicao = $obComponente->getDefinicao();
            $stDefinicao = strtoupper( $stDefinicao );
            if ( !method_exists($obComponente,'getNull') ) {
                die("A classe ".get_class($obComponente)." não possui o método getNull.");
            }
            $boNull = $obComponente->getNull();
            if ( method_exists($obComponente,'getNullBarra') && $this->stName ) {
                if ( $obComponente->getNullBarra() !== null ) {
                    $boNull = $obComponente->getNullBarra();
                }
            }
            switch ($stDefinicao) {
                case "LABEL":
                    $stLimpar .= " if( document.getElementById('".$obComponente->getId()."') )\n";
                    $stLimpar .= "     document.getElementById('".$obComponente->getId()."').innerHTML = '&nbsp;';\n";
                break;
                case "HIDDENEVAL":
                    $stValida .= " eval(document.".$this->getForm().".".$obComponente->getName().".value); \n";
                break;
                case "TIPOBUSCA":
                    $arComponente[$inCont] = $obComponente->getCmpCampo();
                    $boNull = true;
                    $inCont--;
                    continue;
                break;
                case "CHECKBOX":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".checked = false;\n";
                break;
                case "HORA":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value = '';\n";
                break;
                case "TEXT":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                    if ( $obComponente->getMinLength() ) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( stCampo.value.length < ".$obComponente->getMinLength()." ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                    if ( $obComponente->getInteiro() ) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( !isInt( stCampo.value ) ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                    if ( $obComponente->getFloat() ) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( !isFloat( stCampo.value ) ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                    if ( $obComponente->getNaoZero() ) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( parseInt(stCampo.value) == 0 ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!( O valor deve ser maior que zero )\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                    if ( $obComponente->getValidaCaracteres() ) {
                        $obComponente->getStringCaracteres() != "" ? $chAvaliar =  $obComponente->getStringCaracteres() : $chAvaliar = $obComponente->getMascara();
                        $obComponente->getStringCaracteres() != "" ? $mascara   =  "false" : $mascara = "true";
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( !validaCaracteres( stCampo , '".$chAvaliar."', '".$mascara."' ) ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta caracteres inválidos!( \"+stCampo+\" )\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                    if ( $obComponente->getBoCaracteresAceitos() ) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( !validaExpressaoInteira( stCampo , '". $obComponente->getCaracteresAceitos() ."' ) ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta caracteres inválidos! ( \"+stCampo.value+\" )\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                break;
                case "TEXTAREA":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                break;
                case "TEXTBOXSELECT":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obTextBox->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obTextBox->getName().".value='';\n";
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obSelect->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obSelect->getName().".value='';\n";
                break;
                case "CNPJ":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                    if (!$boNull) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( !isCNPJ( stCampo ) ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                break;
                case "CPF":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                    if (!$boNull) {
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if ( !isCPF( stCampo ) ) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }
                break;
                case "DATA":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                    $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                    $stValida .= "     if (stCampo) {\n";
                    $stValida .= "         if (stCampo.value.length > 0) {\n";
                    $stValida .= "             if ( !isData( stCampo.value ) ) {\n";
                    $stValida .= "                 erro = true;\n";
                    $stValida .= "                 mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                    $stValida .= "             }\n";
                    $stValida .= "         }\n";
                    $stValida .= "     }\n";
                break;
                case "PERIODO":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obDataInicial->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obDataInicial->getName().".value='';\n";
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obDataFinal->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obDataFinal->getName().".value='';\n";
                    $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->obDataInicial->getName().";\n";
                    $stValida .= "     stCampo2 = document.".$this->getForm().".".$obComponente->obDataFinal->getName().";\n";

                    if ($obComponente->getExercicio()>0) {
                        $stValida .= "     stExercicio = '".$obComponente->getExercicio()."';\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if (stCampo.value.length > 0) {\n";
                        $stValida .= "             if (stExercicio != stCampo.value.substring(6,10)) {\n";
                        $stValida .= "                 erro = true;\n";
                        $stValida .= "                 mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta o ano da Data Inicial diferente de \"+stExercicio+\"!\";\n";
                        $stValida .= "             }\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                        $stValida .= "     if (stCampo2) {\n";
                        $stValida .= "          if (stCampo2.value.length > 0) {\n";
                        $stValida .= "               if (stExercicio != stCampo2.value.substring(6,10)) {\n";
                        $stValida .= "                   erro = true;\n";
                        $stValida .= "                   mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta o ano da Data Final diferente de \"+stExercicio+\"!\";\n";
                        $stValida .= "               }\n";
                        $stValida .= "          }\n";
                        $stValida .= "     }\n";
                    }
                    if ($obComponente->getValidaExercicio()) {
                        $stValida .= "     if (stCampo && stCampo2) {\n";
                        $stValida .= "         if (stCampo.value.length > 0 || stCampo2.value.length > 0) {\n";
                        $stValida .= "             if (stCampo.value.substring(6,10) != stCampo2.value.substring(6,10)) {\n";
                        $stValida .= "                 erro = true;\n";
                        $stValida .= "                 mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta ano da Data Inicial diferente de ano da Data Final!\";\n";
                        $stValida .= "             }\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    }

                    $stValida .= "     if (stCampo) {\n";
                    $stValida .= "         if (stCampo.value.length > 0) {\n";
                    $stValida .= "             if ( !isData( stCampo.value ) ) {\n";
                    $stValida .= "                 erro = true;\n";
                    $stValida .= "                 mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                    $stValida .= "             }\n";
                    $stValida .= "         }\n";
                    $stValida .= "     }\n";

                    $stValida .= "     if (stCampo2) {\n";
                    $stValida .= "         if (stCampo2.value.length > 0) {\n";
                    $stValida .= "             if ( !isData( stCampo2.value ) ) {\n";
                    $stValida .= "                 erro = true;\n";
                    $stValida .= "                 mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo2.value+\")\";\n";
                    $stValida .= "             }\n";
                    $stValida .= "         }\n";
                    $stValida .= "     }\n";

                    $stValida .= "     stDataInicial = stCampo.value.substring(6,10) + stCampo.value.substring(3,5) + stCampo.value.substring(0,2);\n";
                    $stValida .= "     stDataFinal   = stCampo2.value.substring(6,10) + stCampo2.value.substring(3,5) + stCampo2.value.substring(0,2);\n";
                    $stValida .= "     if (stDataInicial && stDataFinal) {\n";
                    $stValida .= "         if (stDataInicial > stDataFinal) {\n";
                    $stValida .= "             erro = true;\n";
                    $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta Data Inicial maior que Data Final!\";\n";
                    $stValida .= "         }\n";
                    $stValida .= "     }\n";
                break;
                case "PERIODICIDADE":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obDataInicial->getName().$obComponente->getIdComponente()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obDataInicial->getName().$obComponente->getIdComponente().".value='';\n";
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obDataFinal->getName().$obComponente->getIdComponente()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obDataFinal->getName().$obComponente->getIdComponente().".value='';\n";

                    // Alteração para limpar os campos dinâmicos do componente Periodicidade e executar o AJAX que monta o Span com os campos default.
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getPeriodicidade()->getName().$obComponente->getIdComponente()." )\n";
                    $stLimpar .= " ajaxJavaScript('".CAM_FW_INSTANCIAS."processamento/OCPeriodicidade.php?".Sessao::getId()."&inIdComponente=".$obComponente->getIdComponente()."&inCodPeriodo=4','montaSpan');";

                    $stValida .= " stCampo = document.".$this->getForm().".".$obComponente->obDataInicial->getName().$obComponente->getIdComponente().";\n";
                    $stValida .= " stCampo2 = document.".$this->getForm().".".$obComponente->obDataFinal->getName().$obComponente->getIdComponente().";\n";

                    if ($obComponente->getValidaExercicio()) {
                        $stValida .= "     stExercicio = '".$obComponente->getExercicio()."';\n";
                        $stValida .= "     if (stCampo && stCampo2) {\n";
                        $stValida .= "         if (stCampo.value.length > 0) {\n";
                        $stValida .= "              if (stExercicio != stCampo.value.substring(6,10)) {\n";
                        $stValida .= "                  erro = true;\n";
                        $stValida .= "                  mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta o ano diferente de \"+stExercicio+\"!\";\n";
                        $stValida .= "              }\n";
                        $stValida .= "         } else {\n";
                        $stValida .= "              if (stCampo2.value.length > 0) {\n";
                        $stValida .= "                  if (stExercicio != stCampo2.value.substring(6,10)) {\n";
                        $stValida .= "                      erro = true;\n";
                        $stValida .= "                      mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta o ano diferente de \"+stExercicio+\"!\";\n";
                        $stValida .= "                  }\n";
                        $stValida .= "              }\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
}

                    $stValida .= "    if (stCampo) {\n";
                    $stValida .= "        if (stCampo.value.length > 0) {\n";
                    $stValida .= "            if ( !isData( stCampo.value ) ) {\n";
                    $stValida .= "                erro = true;\n";
                    $stValida .= "                mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                    $stValida .= "            }\n";
                    $stValida .= "        }\n";
                    $stValida .= "    }\n";

                    $stValida .= "    if (stCampo2) {\n";
                    $stValida .= "        if (stCampo2.value.length > 0) {\n";
                    $stValida .= "            if ( !isData( stCampo2.value ) ) {\n";
                    $stValida .= "                erro = true;\n";
                    $stValida .= "                mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo2.value+\")\";\n";
                    $stValida .= "            }\n";
                    $stValida .= "        }\n";
                    $stValida .= "    }\n";

                    $stValida .= "     stDataInicial = stCampo.value.substring(6,10) + stCampo.value.substring(3,5) + stCampo.value.substring(0,2);\n";
                    $stValida .= "     stDataFinal   = stCampo2.value.substring(6,10) + stCampo2.value.substring(3,5) + stCampo2.value.substring(0,2);\n";
                    $stValida .= "     if (stDataInicial && stDataFinal) {\n";
                    $stValida .= "         if (stDataInicial > stDataFinal) {\n";
                    $stValida .= "             erro = true;\n";
                    $stValida .= "             mensagem += \"@Campo ".$obComponente->getRotulo()." apresenta Data Inicial maior que Data Final!\";\n";
                    $stValida .= "         }\n";
                    $stValida .= "     }\n";
                break;
                case "EXERCICIO":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->getName().".value='';\n";

                    $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                    $stValida .= "    if (stCampo.value != '') {\n";
                    $stValida .= "        if (stCampo.value < 1949) {\n";
                    $stValida .= "            erro = true;\n";
                    $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(\"+stCampo.value+\")\";\n";
                    $stValida .= "        }\n";
                    $stValida .= "    }\n";
                break;
                case "BUSCAINNER":
                    $stLimpar .= " if( document.getElementById('".$obComponente->getId()."') )\n";
                    $stLimpar .= " document.getElementById('".$obComponente->getId()."').innerHTML = '&nbsp;';";
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->obCampoCod->getName()." )\n";
                    $stLimpar .= " document.".$this->getForm().".".$obComponente->obCampoCod->getName().".value = '';";
                    $stCampoId = "";
                    $stMonitoraBuscaINNER = "";
                    if ($obComponente->getMonitorarCampoCod() and $stCampoId != $obComponente->obCampoCod->getId() ) {
                        $stNomeFuncaoObserver = "Observer".$obComponente->obCampoCod->getId();
                        $stMonitoraBuscaINNER .= "var bo".$stNomeFuncaoObserver."= false;\n";
                        $stMonitoraBuscaINNER .= "function ".$stNomeFuncaoObserver."() {\n";
                        $stMonitoraBuscaINNER .= "     if (bo".$stNomeFuncaoObserver.") {\n";
                        $stMonitoraBuscaINNER .= "          ".$obComponente->obCampoCod->obEvento->getOnChange()."\n";
                        $stMonitoraBuscaINNER .= "          bo".$stNomeFuncaoObserver." = false;\n";
                        $stMonitoraBuscaINNER .= "     }\n";
                        $stMonitoraBuscaINNER .= "}\n";
                        $stMonitoraBuscaINNER .= "new Form.Element.Observer($(\"".$obComponente->obCampoCod->getId()."\"), 1,$stNomeFuncaoObserver);\n";
                        $stCampoId = $obComponente->obCampoCod->getId();
                        $arMonitoraBuscaINNER[$stCampoId] = $stMonitoraBuscaINNER;
                    }
                break;
                case "SPAN":
                    $stLimpar .= " if( document.getElementById('".$obComponente->getId()."') )\n";
                    $stLimpar .= " document.getElementById('".$obComponente->getId()."').innerHTML = '';";
                break;
                case "MULTIPLO":
                    $stCampo  = "document.".$this->getForm().".".$obComponente->getNomeLista2();
                    $stCampo2 = "document.".$this->getForm().".".$obComponente->getNomeLista1();
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getNomeLista2()." && document.".$this->getForm().".".$obComponente->getNomeLista1()." )\n";
                    $stLimpar .= "   passaItem(".$stCampo.",".$stCampo2.", 'tudo');\n";
                    if ( $obComponente->getValorPadrao() ) {
                        $stValida .= "    if (".$stCampo.") {\n";
                        $stValida .= "        if (".$stCampo.".options.length == 0) {\n";
                        $stValida .= "            ".$stCampo.".options[0] = new Option('.','".$obComponente->getValorPadrao()."');\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                        $this->stSalvar .= "    if( ".$stCampo.".options.length == 1 && ";
                        $this->stSalvar .= $stCampo.".options[0].value == '".$obComponente->getValorPadrao()."' ){\n";
                        $this->stSalvar .= "        limpaSelect(".$stCampo.",0);\n";
                        $this->stSalvar .= "    }\n";
                    }
                    $stValida .= "     selecionaTodosSelect(".$stCampo.");\n";
                break;
                case "MOEDA":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= "     document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                    if ( $obComponente->getMaxValue() != null ) {
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if ( !validaValorMaximo( stCampo, \"".$obComponente->getMaxValue()."\", ".$obComponente->getDecimais()." ) ) { \n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(Valor máximo permitido: ".$obComponente->getMaxValue().")\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    } else {
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if ( !validaValorMaximoPermitido( stCampo, ".$obComponente->getDecimais()." ) ) { \n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(Valor máximo permitido: \"+geraValorMaximoPermitido( stCampo, ".$obComponente->getDecimais()." )+\")\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    }
                    if ( $obComponente->getMinValue() != null ) {
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if ( !validaValorMinimo( stCampo, \"".$obComponente->getMinValue()."\", ".$obComponente->getDecimais()." ) ) { \n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(Valor mínimo permitido: ".$obComponente->getMinValue()." )\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    }
                break;
                case "NUMERICO":
                    $stLimpar .= " if( document.".$this->getForm().".".$obComponente->getName()." )\n";
                    $stLimpar .= "     document.".$this->getForm().".".$obComponente->getName().".value='';\n";
                    if ( $obComponente->getMaxValue() != null ) {
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if ( !validaValorMaximo( stCampo, \"".number_format( $obComponente->getMaxValue(), 2, ",", ".")."\", ".$obComponente->getDecimais()." ) ) { \n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(Valor máximo permitido: ".number_format( $obComponente->getMaxValue(), 2, ",", "." ).")\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    }
                    if ( $obComponente->getMinValue() != null ) {
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if ( !validaValorMinimo( stCampo, \"".$obComponente->getMinValue()."\", ".$obComponente->getDecimais()." ) ) { \n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!(Valor mínimo permitido: ".$obComponente->getMinValue()." )\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    }
                    if ( $obComponente->getNaoZero() ) {
                        $flValor = "0,".str_pad ( "",$obComponente->getDecimais(),"0");
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if (stCampo.value == \"".$flValor."\") {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!( O valor deve ser maior que zero )\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    }
                break;

                CASE "SELECT":
                   $stCampo = "document.".$this->getForm().".".$obComponente->getName();
                   $stLimpar .= $stCampo.".selectedIndex = 0 ;\n";
                    if ( $obComponente->getDependente() ) {
                   $stLimpar .="limpaSelect(".$stCampo.",0);\n";
                   $stLimpar .= $stCampo.".options[0] = new Option('Selecione','', 'selected');\n";

                    }
                break;
            }
            if (!$boNull) {
                switch ($stDefinicao) {
                    case "MULTIPLO":
                        $stCampo   = "    document.".$this->getForm().".".$obComponente->getNomeLista2();
                        $stValida .= "    if ($stCampo) {\n";
                        $stValida .= "    var inLength = ".$stCampo.".options.length;\n";
                        $stValida .= "        if ( ( inLength == 1 && trim(".$stCampo.".options[inLength - 1].value) == '') || inLength == 0 ) {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!()\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    break;
                    case "BUSCAINNER":
                        $stCampo   = "    document.".$this->getForm().".".$obComponente->obCampoCod->getName();
                        $stValida .= "    if ($stCampo) {\n";
                        $stValida .= "        if ( trim(".$stCampo.".value) == \"\" ) {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!()\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";

                        if ( ( $obComponente->getId() != "" ) and ( $obComponente->getMostrarDescricao() )  ) {
                            $stInner   = "    document.getElementById('".$obComponente->getId()."')";
                            $stValida .= "    if ($stInner) {\n";
                            $stValida .= "        if ( trim( ".$stInner.".innerHTML ) == \"&nbsp;\" ) {\n";
                            $stValida .= "            erro = true;\n";
                            $stValida .= "            mensagem += \"@Campo Descrição de ".$obComponente->getRotulo()." inválido!()\";\n";
                            $stValida .= "        }\n";
                            $stValida .= "    }\n";
                        }
                    break;
                    case "RADIO":
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if (stCampo.value == \"\") {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!()\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    break;
                    case "PERIODO":
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->obDataInicial->getName().";\n";
                        $stValida .= "    stCampo2 = document.".$this->getForm().".".$obComponente->obDataFinal->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if (stCampo.value == \"\" || stCampo2.value == \"\") {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!()\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    break;
                    case "PERIODICIDADE":
                        $stValida .= "    stCampo = document.".$this->getForm().".".$obComponente->obDataInicial->getName().$obComponente->getIdComponente().";\n";
                        $stValida .= "    stCampo2 = document.".$this->getForm().".".$obComponente->obDataFinal->getName().$obComponente->getIdComponente().";\n";
                        $stValida .= "    if (stCampo && stCampo2) {\n";
                        $stValida .= "        if (stCampo.value == \"\" || stCampo2.value == \"\") {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!()\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    break;
                    case "LISTA":
                        $stValida .= "     stCampo = document.".$this->getForm().".hdn".$obComponente->getId()."NumLinhas;\n";
                        $stValida .= "     if (stCampo) {\n";
                        $stValida .= "         if (stCampo.value < 0) {\n";
                        $stValida .= "             erro = true;\n";
                        $stValida .= "             mensagem += \"@Deve exister pelo menos um item na lista!\";\n";
                        $stValida .= "         }\n";
                        $stValida .= "     }\n";
                    break;

                    DEFAULT:
                        $stValida .= "     stCampo = document.".$this->getForm().".".$obComponente->getName().";\n";
                        $stValida .= "    if (stCampo) {\n";
                        $stValida .= "        if ( trim( stCampo.value ) == \"\" ) {\n";
                        $stValida .= "            erro = true;\n";
                        $stValida .= "            mensagem += \"@Campo ".$obComponente->getRotulo()." inválido!()\";\n";
                        $stValida .= "        }\n";
                        $stValida .= "    }\n";
                    break;
                }
            }
        }
    }

    $stValida .= $this->getComplementoValida();

    if ( !empty( $stLimpar ) ) {
        $stJavaScriptLimpar  = " function limpaFormulario".$this->stName."() {\n ";
        $stJavaScriptLimpar .= $stLimpar;
        $stJavaScriptLimpar .= " if( document.".$this->getForm().".btIncluir".$this->stName." )\n";
        $stJavaScriptLimpar .= "     document.".$this->getForm().".btIncluir".$this->stName.".disabled = false;\n";
        $stJavaScriptLimpar .= " if( document.".$this->getForm().".btAlterar".$this->stName." )\n";
        $stJavaScriptLimpar .= "     document.".$this->getForm().".btAlterar".$this->stName.".disabled = true;\n";
        $stJavaScriptLimpar .= " try { \n";
        $stJavaScriptLimpar .= "     limpaFormulario".$this->stName."Extra(); \n";
        $stJavaScriptLimpar .= " } catch (e) { }\n";
        $stJavaScriptLimpar .= " }\n";

        $this->setLimpar( $stJavaScriptLimpar );
        $stJavaScript .= $stJavaScriptLimpar;

        $stLimpar = str_replace( "\n","",$stLimpar);
        $stLimpar = str_replace("   ","",$stLimpar);
        $stLimpar = str_replace("'","\\'",$stLimpar);
//        $this->setInnerJavaScript( $stLimpar );
    } else {
        $stJavaScript .= $this->getLimpar();
    }

    if ( !empty( $stValida ) ) {
        $stJavaScriptValida  = " function Valida".$this->stName."() {\n ";
        $stJavaScriptValida .= "     var erro = false;\n ";
        $stJavaScriptValida .= "     var mensagem = \"\";\n ";
        $stJavaScriptValida .= $stValida;
        $stJavaScriptValida .= "     if ( (ifila) < fila.length ) {\n";
        $stJavaScriptValida .= "         erro = true;\n";
        $stJavaScriptValida .= "         mensagem += 'Aguarde todos os processos concluírem.';\n";
        $stJavaScriptValida .= "     }\n";

        $stJavaScriptValida .= "     if (erro) { \n";
        $stJavaScriptValida .= "          alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";
        $stJavaScriptValida .= "     }\n";
        $stJavaScriptValida .= " return !erro;\n";
        $stJavaScriptValida .= " }\n";

        $this->setValida( $stJavaScriptValida );
        $stJavaScript .= $stJavaScriptValida;

        $stValida = str_replace("\n","",$stValida);
        $stValida = str_replace("  ","",$stValida);
        $stValida = str_replace("'","\\'",$stValida);
        $this->setInnerJavaScript( $stValida );
    } else {
        $stJavaScript .= $this->getValida();
    }
    $arFuncao = $this->getFuncao();
    if ( count( $arFuncao ) ) {
        foreach ($arFuncao as $stFuncaoAdd) {
            $stJavaScript .= $stFuncaoAdd;
        }
    }
    $stMonitoraBuscaINNER = "";
    foreach ($arMonitoraBuscaINNER as $stMonitora) {
        $stMonitoraBuscaINNER .= $stMonitora;
    }
    $this->setMonitoraBuscaINNER($stMonitoraBuscaINNER);

    $stJavaScript .= $this->stSalvar." }\n";// $this->getSalvar();
    $stJavaScript .= $this->geraHabilitaLayer();
    $stJS  = "<script type=\"text/javascript\">\n";
    $stJS .= $stJavaScript;
    $stJS .= "</script>\n";
    $this->setJavaScript( $stJS );
}

/**
    * Imprime o HTML do Objeto JavaScript na tela (echo)
    * @access Public
*/
function show()
{
    echo $this->montaJavaScript();
}

function montaFixOnChange()
{
    $jsFixOnChange = '';
    if ( is_array( $this->arComponente ) ) {
        foreach ($this->arComponente as $Componente) {
            if ($Componente->obEvento) {
                $obComponente = $Componente;
            } else {
                if ($Componente->obCampoCod) {
                    $obComponente = $Componente->obCampoCod;
                }
            }
            if ($obComponente) {
                if ( $obComponente->obEvento->getOnChange() and ( ( $obComponente->obEvento->getOnBlur()     ) or ( $obComponente->obEvento->getOnKeyPress() )
                                                                 or ( $obComponente->obEvento->getOnKeyUp()    ) ) ) {
                    if ( $obComponente->getId() ) {
                        $jsFixOnChange .= "FixOnChange(document.getElementById('". $obComponente->getId()."')); \n";
                    } else {
                        if ($obComponente->getName())
                            $jsFixOnChange .= "FixOnChange(document.frm." . $obComponente->getName().");\n";
                    }
                }
            }
           unset( $obComponente );
        }
    }

    return $jsFixOnChange;
}

}
?>
