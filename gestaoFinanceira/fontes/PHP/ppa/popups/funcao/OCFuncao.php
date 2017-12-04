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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: OCRecurso.php 32939 2008-09-03 21:14:50Z domluc $
       if (!($rsTipoFiscalizacao->Eof())) {
            $js = "window.document.getElementById('stTipoFiscalizacao').innerHTML = '".$rsTipoFiscalizacao->getCampo('descricao')."';";
        } else {
            $js = "window.document.frm.inTipoFiscalizacao.value                   = '';";
            $js.= "window.document.getElementById('stTipoFiscalizacao').innerHTML = '&nbsp;';";
            $js.= "alertaAviso('@Código do Tipo de Fiscalização inválido (".$_REQUEST['inTipoFiscalizacao'].").','form','erro','".Sessao::getId()."');";
        }
        echo $js;
Casos de uso: uc-02.01.05,uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php"        );

$stCampoCod  	= $_REQUEST['stNomCampoCod'];
$stCampoDesc 	= $_REQUEST['stIdCampoDesc'];
$inCodigo    	= $_REQUEST[ 'inCodFuncao' ];

switch ($_REQUEST["stCtrl"]) {
    case "buscaFuncao":
        $obTPPARegiao = new TOrcamentoFuncao();
        $rsRegiao = new RecordSet();
        $obTPPARegiao->setDado('cod_funcao', $_REQUEST['inCodFuncao']);

        $obTPPARegiao->recuperaPorChave($rsRegiao);

        if ($rsRegiao->inNumLinhas > 0) {
            $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsRegiao->getCampo('descricao')."';";
        } else {
            $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('".$stCampoCod."').value = '';";
            $stJs.= "alertaAviso('@Código da Função (". $inCodigo .") não encontrado.', 'form','erro','".Sessao::getId()."');";
        }
        echo $stJs;
    break;
}
?>
