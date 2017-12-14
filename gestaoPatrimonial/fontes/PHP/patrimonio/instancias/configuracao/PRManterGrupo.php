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
 * Data de Criação: 05/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * @package URBEM
 * @subpackage

 * Casos de uso: uc-03.01.04

 * $Id: PRManterGrupo.php 65901 2016-06-28 14:22:28Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoAnalitica.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoDepreciacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoReavaliacao.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacao.class.php";

$stPrograma = "ManterGrupo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTPatrimonioGrupo = new TPatrimonioGrupo();
$obTPatrimonioGrupoPlanoAnalitica   = new TPatrimonioGrupoPlanoAnalitica();
$obTPatrimonioGrupoPlanoDepreciacao = new TPatrimonioGrupoPlanoDepreciacao();
$obTPatrimonioGrupoPlanoReavaliacao = new TPatrimonioGrupoPlanoReavaliacao();
$obTPatrimonioDepreciacao = new TPatrimonioDepreciacao();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioGrupo );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioGrupoPlanoAnalitica );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioGrupoPlanoDepreciacao );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioGrupoPlanoReavaliacao );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioDepreciacao );

switch ($stAcao) {
    case 'incluir':
        $stFiltro = "
          WHERE  1=1
            AND  nom_grupo = '".$request->get('stDescricaoGrupo')."'
            AND  grupo.cod_natureza = ".$request->get('inCodNatureza')."
        ";
        $obTPatrimonioGrupo->recuperaGrupo( $rsPatrimonioGrupo, $stFiltro );

        if ($rsPatrimonioGrupo->getNumLinhas() <= 0) {

            if($request->get('inCodConta') == "" && ($request->get('inCodContaDoacao') != "" || $request->get('inCodContaPerdaInvoluntaria') != "" || $request->get('inCodContaTransferencia') != ""
                                                      || $request->get('inCodContaVPAAlienacao') != "" || $request->get('inCodContaVPDAlienacao')) ) {
                SistemaLegado::exibeAviso(urlencode('Necessário Informar a Conta Contábil.'),"n_incluir","erro");
                Sessao::encerraExcecao(); die;
            }

            $obTPatrimonioGrupo->setDado( 'cod_natureza', $request->get('inCodNatureza') );
            $obTPatrimonioGrupo->proximoCod( $inCodGrupo );
            $obTPatrimonioGrupo->setDado( 'cod_grupo'   , $inCodGrupo );
            $obTPatrimonioGrupo->setDado( 'nom_grupo'   , $request->get('stDescricaoGrupo') );

            $inDepreciacao = $request->get('inDepreciacao');
            $inDepreciacao = ($inDepreciacao != "" && $inDepreciacao > 0) ? number_format(str_replace(',','.',$inDepreciacao),2,'.','') : '0.00';

            if($request->get('inCodContaDepreciacao') != ""){
                if($request->get('inDepreciacao') == "" || $request->get('inDepreciacao') == 0){
                    SistemaLegado::exibeAviso(urlencode('Como o campo Conta Contábil de Depreciação Acumulada está preenchido, é obrigatório o preenchimento do campo Quota Depreciação Anual.'),"n_incluir","erro");
                    Sessao::encerraExcecao(); die;
                }
            }

            $obTPatrimonioGrupo->setDado( 'depreciacao', $inDepreciacao );
            $obTPatrimonioGrupo->inclusao();

            if($request->get('inCodConta') != ""){
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_grupo'   , $inCodGrupo );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_natureza', $request->get('inCodNatureza') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'exercicio'   , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano'   , $request->get('inCodConta') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_doacao'            , $request->get('inCodContaDoacao') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_perda_involuntaria', $request->get('inCodContaPerdaInvoluntaria') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_transferencia'     , $request->get('inCodContaTransferencia') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_alienacao_ganho'   , $request->get('inCodContaVPAAlienacao')  );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_alienacao_perda'   , $request->get('inCodContaVPDAlienacao')  );
                $obTPatrimonioGrupoPlanoAnalitica->inclusao();
            }

            if($request->get('inCodContaDepreciacao') != ""){
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_grupo'   , $inCodGrupo );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza', $request->get('inCodNatureza') );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'   , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_plano'   , $request->get('inCodContaDepreciacao') );
                $obTPatrimonioGrupoPlanoDepreciacao->inclusao();
            }

            if( ($request->get('inCodNatureza') == 1 || $request->get('inCodNatureza') == 2) && $request->get('inCodContaReavaliacao') != ""){
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_grupo'   , $inCodGrupo );
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_natureza', $request->get('inCodNatureza') );
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'exercicio'   , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_plano'   , $request->get('inCodContaReavaliacao') );
                $obTPatrimonioGrupoPlanoReavaliacao->inclusao();
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Grupo - ".$inCodGrupo,"incluir","aviso", Sessao::getId(), "../");

        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe um grupo com esta descrição para esta natureza'),"n_incluir","erro");
        }

    break;

    case 'alterar' :
        $stFiltro = "
          WHERE nom_grupo          = '".$request->get('stDescricaoGrupo')."'
            AND grupo.cod_natureza =  ".$request->get('inCodNatureza')."
            AND grupo.cod_grupo    <> ".$request->get('inCodGrupo');

        $obTPatrimonioGrupo->recuperaGrupo($rsPatrimonioGrupo, $stFiltro);

        if ($rsPatrimonioGrupo->getNumLinhas() <= 0) {
            
            if($request->get('inCodConta') == "" && ($request->get('inCodContaDoacao') != "" || $request->get('inCodContaPerdaInvoluntaria') != "" || $request->get('inCodContaTransferencia') != ""
                                                     || $request->get('inCodContaVPAAlienacao') != "" || $request->get('inCodContaVPDAlienacao')) ) {
                SistemaLegado::exibeAviso(urlencode('Necessário Informar a Conta Contábil.'),"n_incluir","erro");
                Sessao::encerraExcecao(); die;
            }

            $obTPatrimonioGrupo->setDado('cod_plano_grupo' , $request->get('inCodPlanoDepreciacao'));
            $obTPatrimonioGrupo->setDado('cod_grupo'       , $request->get('inCodGrupo'));
            $obTPatrimonioGrupo->setDado('cod_natureza'    , $request->get('inCodNatureza'));
            $obTPatrimonioGrupo->recuperaGrupoPlanoDepreciacao( $rsGrupoPlanoDepreciacao );

            if ($rsGrupoPlanoDepreciacao->getNumLinhas() >= 1 && $rsGrupoPlanoDepreciacao->getCampo("cod_plano") != $request->get('inCodContaDepreciacao')) {
                SistemaLegado::exibeAviso(urlencode('Já existem depreciações lançadas para este Grupo. Anule-as para alterar a Conta Contábil de Depreciação.'),"n_incluir","erro");
                Sessao::encerraExcecao();
                die();
            } else {

                //deleta da table grupo_plano_depreciacao
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_grupo'    , $request->get('inCodGrupo') );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'    , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoDepreciacao->exclusao();

                //inclui na table grupo_plano_depreciacao
                if($request->get('inCodContaDepreciacao') != ""){
                    $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'    , Sessao::getExercicio() );
                    $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
                    $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_plano'    , $request->get('inCodContaDepreciacao') );
                    $obTPatrimonioGrupoPlanoDepreciacao->inclusao();
                }
            }

            $obTPatrimonioGrupo->setDado( 'cod_natureza', $request->get('inCodNatureza') );
            $obTPatrimonioGrupo->setDado( 'cod_grupo'   , $request->get('inCodGrupo') );
            $obTPatrimonioGrupo->setDado( 'nom_grupo'   , $request->get('stDescricaoGrupo') );

            $inDepreciacao = $request->get('inDepreciacao');

            $inDepreciacao = ($inDepreciacao != "" ) ? number_format(str_replace(',','.',$inDepreciacao),2,'.','') : '0.00';

            if($request->get('inCodContaDepreciacao') != ""){
                if($request->get('inDepreciacao' == "") || $inDepreciacao == 0.00){
                    SistemaLegado::exibeAviso(urlencode('Como o campo Conta Contábil de Depreciação Acumulada está preenchido, é obrigatório o preenchimento do campo Quota Depreciação Anual.'),"n_incluir","erro");
                    Sessao::encerraExcecao(); die;
                }
            }

            $obTPatrimonioGrupo->setDado( 'depreciacao', $inDepreciacao );
            $obTPatrimonioGrupo->alteracao();

            //deleta da table grupo_plano_analitica
            $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
            $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_grupo'    , $request->get('inCodGrupo') );
            $obTPatrimonioGrupoPlanoAnalitica->setDado( 'exercicio'    , Sessao::getExercicio() );
            $obTPatrimonioGrupoPlanoAnalitica->exclusao();

            // se os campos de doacao estiverem preenchidos, o campo de conta contabil deve ser preenchido tbm
            if($request->get('inCodConta') != ""){
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'exercicio'    , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano'    , $request->get('inCodConta') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_doacao'            , $request->get('inCodContaDoacao') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_perda_involuntaria', $request->get('inCodContaPerdaInvoluntaria') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_transferencia'     , $request->get('inCodContaTransferencia') );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_alienacao_ganho'   , $request->get('inCodContaVPAAlienacao')  );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano_alienacao_perda'   , $request->get('inCodContaVPDAlienacao')  );
                $obTPatrimonioGrupoPlanoAnalitica->inclusao();
            }

            //deleta da table grupo_plano_reavaliacao
            $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
            $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_grupo'    , $request->get('inCodGrupo') );
            $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'exercicio'    , Sessao::getExercicio() );
            $obTPatrimonioGrupoPlanoReavaliacao->exclusao();

            //inclui na table grupo_plano_reavaliacao
            if( ($request->get('inCodNatureza') == 1 || $request->get('inCodNatureza') == 2) && $request->get('inCodContaReavaliacao') != ""){
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_grupo'   , $request->get('inCodGrupo') );
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_natureza', $request->get('inCodNatureza') );
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'exercicio'   , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_plano'   , $request->get('inCodContaReavaliacao') );
                $obTPatrimonioGrupoPlanoReavaliacao->inclusao();
            }

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Grupo - ".$request->get('inCodGrupo'),"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe um grupo com esta descrição para esta natureza'),"n_incluir","erro");
        }

    break;

    case 'excluir' :
        $obTPatrimonioGrupoPlanoAnalitica->setDado('cod_grupo'    , $request->get('inCodGrupo') );
        $obTPatrimonioGrupoPlanoAnalitica->setDado('cod_natureza' , $request->get('inCodNatureza') );
        $obTPatrimonioGrupoPlanoAnalitica->setDado('exercicio'    , Sessao::getExercicio() );
        $obTPatrimonioGrupoPlanoAnalitica->exclusao();

        //deleta da table grupo_plano_depreciacao
        $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
        $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_grupo'    , $request->get('inCodGrupo') );
        $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'    , Sessao::getExercicio() );
        $obTPatrimonioGrupoPlanoDepreciacao->exclusao();

        //deleta da table grupo_plano_reavaliacao
        $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_natureza' , $request->get('inCodNatureza') );
        $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'cod_grupo'    , $request->get('inCodGrupo') );
        $obTPatrimonioGrupoPlanoReavaliacao->setDado( 'exercicio'    , Sessao::getExercicio() );
        $obTPatrimonioGrupoPlanoReavaliacao->exclusao();

        $obTPatrimonioGrupo->setDado('cod_natureza' , $request->get('inCodNatureza') );
        $obTPatrimonioGrupo->setDado('cod_grupo'    , $request->get('inCodGrupo') );
        $obTPatrimonioGrupo->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Grupo - ".$request->get('inCodGrupo'),"excluir","aviso", Sessao::getId(), "../");

    break;

}

Sessao::encerraExcecao();

?>