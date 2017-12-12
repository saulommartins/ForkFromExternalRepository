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
    * Página de Listagem de Itens
    * Data de Criação   : 22/09/2008

    * @author Desenvolvedor: Heleno Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @ignore

    * Casos de uso: uc-02.09.03
*/

/*
$Log$
Revision 1.1  2007/06/19 15:04:31  leandro.zis
uc 02.09.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_PPA_MAPEAMENTO."TPPARegiao.class.php" );

include_once CAM_GF_PPA_CLASSES."negocio/RPPAManterRegiao.class.php";
include_once CAM_GF_PPA_CLASSES."visao/VPPAManterRegiao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegioes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCaminho   = CAM_GF_PPA_INSTANCIAS."regioes/";

$obTPPARegiao = new TPPARegiao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

$pg  = $request->get('pg', 0);
$pos = $request->get('pos', 0);

if ( !Sessao::read('paginando') ) {
    foreach ($request->getAll() as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $pg);
    Sessao::write('pos', $pos);
    Sessao::write('paginando', true);
    Sessao::write('filtro', $arFiltro);
} else {
    Sessao::write('pg', $pg);
    Sessao::write('pos',$pos);
    $arFiltro = Sessao::read('filtro');
}
// Foi neecessário reescrever sobre o Objeto Request para receber os novos parametros.
$request = new Request($_REQUEST);

$obRegra = new RPPAManterRegiao();
$obVisao = new VPPAManterRegiao( $obRegra );
$obVisao->listar($request);

?>
