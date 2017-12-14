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
    * Página de Formulário do Estagiário
    * Data de Criação: 14/06/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 23636 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-29 11:30:47 -0300 (Sex, 29 Jun 2007) $

    * Casos de uso: uc-04.00.00
*/

/*
$Log$
Revision 1.3  2007/06/29 14:30:47  souzadl
Bug #9518#

Revision 1.2  2007/06/29 14:29:20  souzadl
Bug #9518#

Revision 1.1  2007/06/14 14:15:14  souzadl
Bug #9309#

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos');

//Define o nome dos arquivos PHP
$stPrograma = "EntidadeUsuario";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&acao=$stAcao";
$pgProc = "PR".$stPrograma.".php";

$userCgm = Sessao::read('numCgm');

if ($userCgm == 0) {

    Sessao::setTrataExcecao(true);

    $inCodEntidade = $_REQUEST["inCodEntidade"];

    include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
    $obTOrcamentoEntidade = new TOrcamentoEntidade();
    $stFiltro  = " AND e.exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= " AND e.cod_entidade = ".$inCodEntidade;
    $obTOrcamentoEntidade->recuperaEntidades($rsEntidades,$stFiltro);

    include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);

    //Administrador do sistema
    if ($rsEsquemas->getNumLinhas() == -1) {
        $obTEntidade->setDado("cod_entidade",$inCodEntidade);
        $obTEntidade->replicarEsquemasRH($rsEsquemas);
        $stMensagem = "Os esquemas para a entidade ".$rsEntidades->getCampo("nom_cgm")." foram criados corretamente.";
    } else {
        $stMensagem = "A entidade ".$rsEntidades->getCampo("nom_cgm")." já foi incluída na Gestão de Recursos Humanos.";
    }

    Sessao::encerraExcecao();
    SistemaLegado::LiberaFrames(true,false);
    sistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
} else {
    $stMensagem = "A inclusão de entidades só pode ser feita pelo Administrador do sistema!";
    SistemaLegado::LiberaFrames(true,false);
    sistemaLegado::alertaAviso($pgForm,$stMensagem,"erro","erro", Sessao::getId(), "../");
}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
