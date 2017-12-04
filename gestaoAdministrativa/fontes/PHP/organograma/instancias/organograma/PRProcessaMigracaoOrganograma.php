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
/*
    * Página de Processamento de Dados
    * Data de Criação   : 06/01/2009

    * @author Analista      Gelson Gonçalves
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ProcessaMigracaoOrganograma";
$pgForm     = "FM".$stPrograma.".php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$stAcao = $request->get('stAcao');

switch ($stAcao) {

    case 'migra':

        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $obTAdministracaoConfiguracao->setDado("exercicio"  , '2009'              );
        $obTAdministracaoConfiguracao->setDado("cod_modulo" , '19'                );
        $obTAdministracaoConfiguracao->setDado("parametro"  , 'migra_organograma' );
        $obTAdministracaoConfiguracao->recuperaPorChave($rsRecordSet);

        if ($rsRecordSet->getCampo('valor') == "false") {

            include_once CAM_GA_ORGAN_MAPEAMENTO."TMigraOrganograma.class.php";
            include_once CAM_GA_ORGAN_MAPEAMENTO."TMigraOrganogramaLocal.class.php";

            $obTMigraOrganograma = new TMigraOrganograma;
            $obTMigraOrganograma->recuperaMsgMigra($rsRecordSet);

            # Verificação da efetividade da PL.
            $obTMigraOrganograma->recuperaMigraTotalidade($rsSetor);

            # Verificação da efetividade da PL.
            $obTMigraOrganogramaLocal = new TMigraOrganogramaLocal;
            $obTMigraOrganogramaLocal->recuperaMigraTotalidade($rsLocal);

            $stError = "";

            if ($rsSetor->getCampo('finalizado') == "false") {
                $stError .= "Setor Não Configurado.";
                $stJs .= 'f.Ok.disabled = true; ';
            }

            if ($rsLocal->getCampo('finalizado') == "false") {
                $stError .= "Local Não Configurado.";
                $stJs .= 'f.Ok.disabled = true; ';
            }

            $stCamFram = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/";
            $stCamAdm  = "../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/mapeamento/";
            copy($stCamFram."Sessao_migra.class.php", $stCamFram."Sessao.class.php"                             );
            copy($stCamAdm."TAdministracaoUsuario_migra.class.php", $stCamAdm."TAdministracaoUsuario.class.php" );
            copy($stCamAdm."TAdministracaoAcao_migra.class.php", $stCamAdm."TAdministracaoAcao.class.php"       );

            if (empty($stError))
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Migração do Organograma concluído com sucesso!","aviso","aviso", Sessao::getId(), "../");
            else
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stError,"erro","erro", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Migração do Organograma já foi executada anteriormente!","erro","erro", Sessao::getId(), "../");
        }

    break;

}

Sessao::encerraExcecao();

?>
