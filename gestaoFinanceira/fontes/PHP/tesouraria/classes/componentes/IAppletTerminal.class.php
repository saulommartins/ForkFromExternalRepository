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
* Padronizar a Applet para validar se o terminal/usuário é válido
* Data de Criação: 17/11/2005

* @author Analista: Lucas Leusin
* @author Desenvolvedor: Anderson R. M. Buzo

* @package framework
* @subpackage componentes

    $Id: IAppletTerminal.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-02.04.18,uc-02.04.05,uc-02.04.04,uc-02.04.09,uc-02.04.17

*/
include_once ( CLA_APPLET );

class IAppletTerminal extends Applet
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    /**
        * @access Private
        * @var String
    */
    public $stNameCodTerminal;
    /**
        * @access Private
        * @var String
    */
    public $stNameTimestampTerminal;
    /**
        * @access Private
        * @var String
    */
    public $stNameTimestampUsuario;
    /**
        * @access Private
        * @var String
    */
    public $stSaldoCaixa;
    /**
        * @access Private
        * @var object
    */
    public $rsCaixaEntidade;
    /**
        * @access Private
        * @var boolean
    */
    public $boMostraSaldoCaixa;
    /**
        * @access Private
        * @var integer
    */
    public $inCodEntidade;

    /**
        * @access Public
        * @param String $valor
    */
    public function setNameCodTerminal($valor) { $this->stNameCodTerminal       = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setNameTimestampTerminal($valor) { $this->stNameTimestampTerminal = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setNameTimestampUsuario($valor) { $this->stNameTimestampUsuario  = $valor; }

    /**
        * @access Public
        * @return String $valor
    */
    public function getNameCodTerminal() { return $this->stNameCodTerminal;       }
    /**
        * @access Public
        * @return String $valor
    */
    public function getNameTimestampTerminal() { return $this->stNameTimestampTerminal; }
    /**
        * @access Public
        * @return String $valor
    */
    public function getNameTimestampUsuario() { return $this->stNameTimestampUsuario;  }
    /**
        * @access Public
        * @return String $valor
    */
    public function getSaldoCaixa() { return $this->stSaldoCaixa;  }

    /**
        * Metodo Construtor
        * @access Public
    */
    public function IAppletTerminal($obForm)
    {
        $this->obForm = $obForm;

        if (isset($_REQUEST['inCodEntidade']) AND !is_array($_REQUEST['inCodEntidade']) AND !strpos($_REQUEST['inCodEntidade'], ',')) {
            Sessao::write('cod_entidade',$_REQUEST['inCodEntidade'],true);
            $this->inCodEntidade = $_REQUEST['inCodEntidade'];
        } elseif (!isset($_REQUEST['inCodEntidade']) && (Sessao::read('cod_entidade') != '')) {
            $this->inCodEntidade = Sessao::read('cod_entidade');
        } else {
            $this->inCodEntidade = 0;
        }

    // exibe o saldo da entidade no terminal para ativar passar this->inCodEntidade para true
        if ($this->inCodEntidade != '' && $this->inCodEntidade != 0) {
            $this->rsCaixaEntidade = new RecordSet();
            $this->setSaldoCaixa();
        }

        $this->setWidth   ( 0                             );
        $this->setHeight  ( 0                             );

        $this->stNameCodTerminal       = "inCodTerminal";
        $this->stNameTimestampTerminal = "stTimestampTerminal";
        $this->stNameTimestampUsuario  = "stTimestampUsuario";
    }

    public function montaHTML()
    {
        $obHidden = new Hidden();
        $obHidden->setName( 'stHashMac' );
        $obHidden->setID  ( 'stHashMac' );
        $obHidden->montaHtml();

        $obHdnCodTerminal = new Hidden;
        $obHdnCodTerminal->setName  ( $this->stNameCodTerminal );
        $obHdnCodTerminal->montaHtml();

        $obHdnTimestampTerminal = new Hidden;
        $obHdnTimestampTerminal->setName  ( $this->stNameTimestampTerminal );
        $obHdnTimestampTerminal->montaHtml();

        $obHdnTimestampUsuario = new Hidden;
        $obHdnTimestampUsuario->setName  ( $this->stNameTimestampUsuario );
        $obHdnTimestampUsuario->montaHtml();

        $stHtml  = $obHidden->getHtml();
        $stHtml .= $obHdnCodTerminal->getHtml();
        $stHtml .= $obHdnTimestampTerminal->getHtml();
        $stHtml .= $obHdnTimestampUsuario->getHtml();

        $stHtml .= "<script type=\"text/javascript\">                                                                   \n";
        $stHtml .= "var stHashMac; \n";
        $stHtml .= "function handleHttpResponse() {                                                         \n";
        $stHtml .= "  if (http.readyState == 4) {                                                                      \n";
        $stHtml .= "     if (http.responseText.indexOf('invalid') == -1) {                                             \n";
        $stHtml .= "        // usa XML                                                                                   \n";
        $stHtml .= "        f = parent.frames['telaPrincipal'].document.".$this->obForm->getName().";                    \n";
        $stHtml .= "        var xmlDocument = http.responseXML;                                                          \n";
        $stHtml .= "        var cod_terminal       = xmlDocument.getElementsByTagName('cod_terminal').item(0).firstChild.data;       \n";
        $stHtml .= "        var timestamp_terminal = xmlDocument.getElementsByTagName('timestamp_terminal').item(0).firstChild.data; \n";
        $stHtml .= "        var timestamp_usuario  = xmlDocument.getElementsByTagName('timestamp_usuario').item(0).firstChild.data;  \n";
        $stHtml .= "        f.".$this->getNameCodTerminal().".value = cod_terminal;                                      \n";
        $stHtml .= "        f.".$this->getNameTimestampTerminal().".value = timestamp_terminal;                          \n";
        $stHtml .= "        f.".$this->getNameTimestampUsuario().".value = timestamp_usuario;                            \n";
        $stHtml .= "        if (cod_terminal != '') {                                                                \n";
        $stHtml .= "           top.telaStatus.document.getElementById('stTerminalLogado').innerHTML = ' - Terminal Logado: '+cod_terminal";
        if ($this->boMostraSaldoCaixa) {
            $stHtml .= "+' - Saldo da Conta Caixa: R$ ".$this->stSaldoCaixa."' :: Terminal logado: ".$this->getNameCodTerminal();
        }
        $stHtml .= "        }                                                                                    \n";
        $stHtml .= "    } else {                                                                                   \n";
        $stHtml .= "      stRedirect = '".CAM_GF_TES_INSTANCIAS."configuracao/FMPermissaoNegada.php?".Sessao::getId()."';     \n";
        $stHtml .= "      parent.frames['telaPrincipal'].location = stRedirect + '&stHashMac=' + stHashMac;       \n";
        $stHtml .= "      top.telaStatus.document.getElementById('stTerminalLogado').innerHTML = '&nbsp';         \n";

        $stHtml .= "    }                                                                                          \n";
        $stHtml .= "    LiberaFrames(true,false);                                                                      \n";
        $stHtml .= "  }                                                                                                \n";
        $stHtml .= "}                                                                                                  \n";
        $stHtml .= "function getHashMd5Mac() {                                                           \n";
        $stHtml .= "    if (http != false) {                                                            \n";
        $stHtml .= "        var bm        = document.applets[0];                                         \n";
        $stHtml .= "        stHashMac     = '".SistemaLegado::gerarCodigoTerminal()."'                     \n";
        $stHtml .= "        var stCtrl    = 'validaTerminal';                                            \n";
        $stHtml .= "        var url = '".CAM_GF_TES_INSTANCIAS."processamento/OCAppletTerminal.php';     \n";
        $stHtml .= "        var url = url + '?".Sessao::getId()."&stHashMac='+stHashMac+'&stCtrl='+stCtrl;   \n";
        $stHtml .= "        http.open('GET', url, true);                                                 \n";
        $stHtml .= "        http.onreadystatechange = handleHttpResponse;                   \n";
        $stHtml .= "        isWorking = true;                                                            \n";
        $stHtml .= "        http.send(null);                                                             \n";
        $stHtml .= "    }                                                                                \n";
        $stHtml .= "}                                                           \n";
        $stHtml .= "                                                            \n";
        $stHtml .= "function getHTTPObject() {                                  \n";
        $stHtml .= "  var xmlhttp;                                              \n";
        $stHtml .= "  /*@cc_on                                                  \n";
        $stHtml .= "  @if (@_jscript_version >= 5)                              \n";
        $stHtml .= "    try {                                                   \n";
        $stHtml .= "      xmlhttp = new ActiveXObject(\"Msxml2.XMLHTTP\");      \n";
        $stHtml .= "    } catch (e) {                                           \n";
        $stHtml .= "      try {                                                 \n";
        $stHtml .= "        xmlhttp = new ActiveXObject(\"Microsoft.XMLHTTP\"); \n";
        $stHtml .= "      } catch (E) {                                         \n";
        $stHtml .= "        xmlhttp = false;                                    \n";
        $stHtml .= "      }                                                     \n";
        $stHtml .= "    }                                                       \n";
        $stHtml .= "  @else                                                     \n";
        $stHtml .= "  xmlhttp = false;                                          \n";
        $stHtml .= "  @end@*/                                                   \n";
        $stHtml .= "  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {   \n";
        $stHtml .= "    try {                                                   \n";
        $stHtml .= "      xmlhttp = new XMLHttpRequest();                       \n";
        $stHtml .= "      xmlhttp.overrideMimeType(\"text/xml\");               \n";
        $stHtml .= "    } catch (e) {                                           \n";
        $stHtml .= "      xmlhttp = false;                                      \n";
        $stHtml .= "    }                                                       \n";
        $stHtml .= "  }                                                         \n";
        $stHtml .= "  return xmlhttp;                                           \n";
        $stHtml .= "}                                                           \n";
        $stHtml .= "BloqueiaFrames(true,false);";
        $stHtml .= "var http = getHTTPObject(); // Criando objeto HTTP          \n";
        $stHtml .= "getHashMd5Mac();                                            \n";
        $stHtml .= "</script>                                                   \n";
        $this->setHtml( $stHtml );
    }

    /**
        * @access Public
        * @return void
    */
    public function setSaldoCaixa()
    {
        include_once(CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php");
    include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaExtratoBancario.class.php");
    $obFTesourariaExtratoBancario = new FTesourariaExtratoBancario;

    $rsDadosBancarios = new RecordSet();
        $stOrder = "";
        $stFiltro = "";

        $this->getCodPlanoEntidade($this->rsCaixaEntidade);
        $inCodPlano = $this->rsCaixaEntidade->getCampo('valor');

        $obFTesourariaExtratoBancario = new FTesourariaExtratoBancario;
        $obFTesourariaExtratoBancario->setDado("inCodPlano"    , $inCodPlano );
        $obFTesourariaExtratoBancario->setDado("stExercicio"   , Sessao::getExercicio());
        $obFTesourariaExtratoBancario->setDado("stDtInicial" , '01/01/'.Sessao::getExercicio());
        $obFTesourariaExtratoBancario->setDado("stDtFinal"   , '31/12/'.Sessao::getExercicio());
        $obFTesourariaExtratoBancario->setDado("boMovimentacao", "true" );
        $obErro = $obFTesourariaExtratoBancario->recuperaSaldoAnteriorAtual( $rsSaldoAnteriorAtual, $stFiltro, $stOrder );
        $saldoAtual= doubleval($rsSaldoAnteriorAtual->getCampo("fn_saldo_conta_tesouraria"));

        if ($saldoAtual<0) {
           $this->stSaldoCaixa  = "<span style=\"color:red;\">-".number_format(abs($saldoAtual), 2, ',', '.').'</span>';
        } else {
           $this->stSaldoCaixa  = number_format($saldoAtual, 2, ',', '.');
        }
    }

    public function getCodPlanoEntidade(&$rsCaixaEntidade)
    {
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/TAdministracaoConfiguracaoEntidade.class.php';
        $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();

        $stFiltro = " where parametro = 'conta_caixa' and cod_entidade = ".$this->inCodEntidade." and exercicio = '".Sessao::getExercicio()."'";

        $obTAdministracaoConfiguracaoEntidade->recuperaTodos($rsCaixaEntidade, $stFiltro);
    }

}
?>
