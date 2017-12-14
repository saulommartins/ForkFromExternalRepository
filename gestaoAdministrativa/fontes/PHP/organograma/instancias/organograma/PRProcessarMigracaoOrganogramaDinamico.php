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
  * Página de Processamento para Migrar Organograma Dinamico
  * Data de criação: 15/04/2009

  * @author Analista: Gelson Wolowski   <gelson.goncalves@cnm.org.br>
  * @author Programador: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

  * @package     Gestao Administrativa
  * @subpackage  Organograma

  $Id:$

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TConfigurarMigracaoOrganogramaDinamico.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrganograma.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrgaoDescricao.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrganograma.class.php';
include_once CAM_GA_ORGAN_NEGOCIO.'ROrganogramaOrganograma.class.php';

$stPrograma = "ProcessarMigracaoOrganogramaDinamico";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

$stAcao = $request->get('stAcao');

$obTConfiguracaoMigracaoOrganogramaDinamico = new TConfigurarMigracaoOrganogramaDinamico;
$obTOrganogramaOrganograma                  = new TOrganogramaOrganograma;

switch ($stAcao) {

    case 'migrar':

        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->setDado("exercicio"  , Sessao::getExercicio());
        $obTAdministracaoConfiguracao->setDado("cod_modulo" , '19'                  );
        $obTAdministracaoConfiguracao->setDado("parametro"  , 'migra_orgao'         );
        $obTAdministracaoConfiguracao->recuperaPorChave($rsRecordSet);

        if ($rsRecordSet->getCampo('valor') == "true") {

            $stError = "";

            # Verificação da configuração do organograma.
            $obTConfiguracaoMigracaoOrganogramaDinamico->recuperaMigraTotalidade($rsOrgao);

            if ($rsOrgao->getCampo('finalizado') == "false") {
                $stError .= "Configuração Não Configurada.";
                $stJs .= "jQuery('#Ok').attr('disabled', 'disabled'); \n";
            }

            if (empty($stError)) {

                # Caso o órgão do organograma vinculado ao usuário tenha sido modificado,
                # atualiza na sessão com o novo escolhido.
                $inNovoCodOrgao = SistemaLegado::pegaDado('cod_orgao_new', 'organograma.de_para_orgao', 'WHERE cod_orgao = '.Sessao::read('codOrgao'));

                if (is_numeric($inNovoCodOrgao)) {
                    $obTOrganogramaOrgaoDescricao = new TOrganogramaOrgaoDescricao;
                    $obTOrganogramaOrgaoDescricao->setDado('cod_orgao', $inNovoCodOrgao);
                    $obTOrganogramaOrgaoDescricao->recuperaUltimoOrgaoDescricao($rsOrgaoDescricao);

                    # Atualiza o id e a última descrição do órgão na sessão.
                    Sessao::write('codOrgao', $inNovoCodOrgao, true);
                    Sessao::write('orgao'   , $rsOrgaoDescricao->getCampo('descricao'), true);
                }

                # Recupera o novo cod_organograma do organograma selecionado.
                $inCodOrgaoPadrao       = SistemaLegado::pegaDado('cod_orgao_new'   , 'organograma.de_para_orgao' , 'WHERE 1=1 LIMIT 1'                    );

                $inCodOrganogramaPadrao = SistemaLegado::pegaDado('cod_organograma' , 'organograma.orgao_nivel'   , 'WHERE cod_orgao = '.$inCodOrgaoPadrao );

                $obTOrganogramaOrganograma->setDado('cod_organograma' , $inCodOrganogramaPadrao );
                $obTOrganogramaOrganograma->setDado('implantacao'     , date("d/m/Y",time()));
                $obTOrganogramaOrganograma->alteracao();

                # Recupera o Organograma Ativo.
                $inCodOrganogramaAtivo  = SistemaLegado::pegaDado('cod_organograma' , 'organograma.organograma'   , 'WHERE ativo = true');

                # Faz o chamado para o método que possui a PL que efetiva as alterações.
                $obTConfiguracaoMigracaoOrganogramaDinamico->recuperaMsgMigra($rsRecordSet);

                # Coloca o organograma selecionado como ativo no sistema.
                $obRegraOrganograma        = new ROrganogramaOrganograma;
                $obTOrganogramaOrganograma = new TOrganogramaOrganograma;

                $stFiltro = " WHERE cod_organograma IN (".$inCodOrganogramaAtivo.", ".$inCodOrganogramaPadrao.")";
                $obRegraOrganograma->listarOrganogramas($rsOrganograma, '', '', $stFiltro);

                # Utilizado para ativar/desativar os Organogramas que estao sendo migrados.
                while (!$rsOrganograma->eof()) {
                    $boAtivo = ($rsOrganograma->getCampo('cod_organograma') == $inCodOrganogramaPadrao) ? 'true' : 'false';

                    $obTOrganogramaOrganograma->setDado('cod_organograma' , $rsOrganograma->getCampo('cod_organograma'));
                    $obTOrganogramaOrganograma->setDado('cod_norma'       , $rsOrganograma->getCampo('cod_norma'));
                    $obTOrganogramaOrganograma->setDado('implantacao'     , $rsOrganograma->getCampo('implantacao'));
                    $obTOrganogramaOrganograma->setDado('ativo'           , $boAtivo);
                    $obErro = $obTOrganogramaOrganograma->alteracao();

                    $rsOrganograma->proximo();
                }

                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Migração do Organograma concluído com sucesso!","aviso","aviso", Sessao::getId(), "../");
            } else {
               SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stError,"erro","erro", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração da Migração do Organograma não está finalizada!","erro","erro", Sessao::getId(), "../");
        }

    break;
}

Sessao::encerraExcecao();

?>
