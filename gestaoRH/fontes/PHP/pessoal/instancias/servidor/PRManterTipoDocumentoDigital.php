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
    * Página de Processamento de Inclusao/Alteracao de Tipo Documento Digital
    * Data de criação   : 05/06/2016

    * @author Michel Teixeira

    * @ignore

    $Id: PRManterTipoDocumentoDigital.php 66021 2016-07-07 18:41:02Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDocumentoDigital.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoDocumentoDigital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

$obErro = new Erro;
$obTransacao = new Transacao();
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

$obTPessoalTipoDocumentoDigital = new TPessoalTipoDocumentoDigital();

if ( !$obErro->ocorreu() ) {
    switch ($stAcao) {
        case 'incluir':
            $obErro = $obTPessoalTipoDocumentoDigital->proximoCod( $inCodTipo, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTPessoalTipoDocumentoDigital->setDado( 'cod_tipo', $inCodTipo );
                $obTPessoalTipoDocumentoDigital->setDado( 'descricao', $request->get('stDescricao') );

                $obErro = $obTPessoalTipoDocumentoDigital->inclusao($boTransacao);
            }
        break;
    }
    
    
    if (!$obErro->ocorreu()){
        $stJs = '';
        $stJs .= "var lengthSelect = window.parent.window.opener.document.getElementById('inTipoDocDigital').length; \n";
        $stJs .= "window.parent.window.opener.document.getElementById('inTipoDocDigital').options[lengthSelect] = new Option(\"".$request->get('stDescricao')."\", ".$inCodTipo."); \n";
        $stJs .= "window.parent.window.close(); \n";
        SistemaLegado::executaFrameOculto($stJs);
    }
}

if ($obErro->ocorreu())
    SistemaLegado::alertaAviso(CAM_GRH_PES_INSTANCIAS."servidor/FMManterTipoDocumentoDigital.php", $obErro->getDescricao());

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalTipoDocumentoDigital );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
