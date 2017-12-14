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
* Arquivo instância para popup para inserir CBO
* Data de Criação: 13/06/2013
* @author Desenvolvedor: Evandro Melos
* $Id: PRInserirCBO.php 63966 2015-11-11 20:22:59Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalCbo.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "InserirCBO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTPessoalCBO = new TPessoalCbo();
$obErro = new Erro();
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$stOrder = '';

$obTPessoalCBO->setDado('codigo'    ,$request->get('stNumCbo')  );
$obTPessoalCBO->setDado('descricao' ,$request->get('stNomeCbo') );
$obTPessoalCBO->setDado('dt_inicial',$request->get('dtInicial') );
$obTPessoalCBO->setDado('dt_final'  ,$request->get('dtFinal')   );

$stFiltro = " WHERE codigo = ".$request->get('stNumCbo')." and descricao ilike '".$request->get('stNomeCbo')."'";
$obErro = $obTPessoalCBO->recuperaTodos($rsRecord, $stFiltro, $stOrder, $boTransacao);

if ( $rsRecord->getNumLinhas() < 1 && !$obErro->ocorreu() ) {
    $obErro = $obTPessoalCBO->inclusao($boTransacao);

    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE codigo = ".trim($request->get('stNumCbo'));
        $obErro = $obTPessoalCBO->recuperaTodos($rsCBO, $stFiltro, $stOrder, $boTransacao);

        if( !$obErro->ocorreu() ){
            $stJs  = "window.parent.window.opener.document.frm.inNumCBO.value = \"".$request->get('stNumCbo')."\";                      \n";
            $stJs .= "window.parent.window.opener.document.getElementById('inNomCBO').innerHTML = \"".$request->get('stNomeCbo')."\";   \n";
            $stJs .= "window.parent.window.opener.document.frm.inCodCBO.value = \"".$rsCBO->getCampo('cod_cbo')."\";                    \n";
            $stJs .= "window.parent.close();                                                                                            \n";

            SistemaLegado::executaFrameOculto($stJs);
        }
    }
}
elseif( !$obErro->ocorreu() )
    SistemaLegado::alertaAviso($pgForm,"Registro já cadastrado!",'form','erro', Sessao::getId(),'');

if( $obErro->ocorreu() )
    SistemaLegado::alertaAviso($pgForm,$obErro->getDescricao(),'form','erro', Sessao::getId(),'');

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPessoalCBO );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
