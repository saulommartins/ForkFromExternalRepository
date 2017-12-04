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
    * Página de Processamento do Configuração SEFIP
    * Data de Criação: 12/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-04.08.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                            );
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACategoriaSefip.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSEFIP";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento($obTAdministracaoConfiguracao);

$obErro = new Erro();

switch ($stAcao) {

    case "configurar":
        $inCodModulo = 40;

        if (count(Sessao::read("arModalidades")) == 0) {
            Sessao::write('NOVAacao',$stAcao);
            SistemaLegado::alertaAviso($pgForm,"É necessário inserir ao menos 1 (uma) Modalidades de Recolhimento!","n_incluir","erro", Sessao::getId(), "../");
        } else {
            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
            $obTIMACategoriaSefip = new TIMACategoriaSefip();

            $obTAdministracaoConfiguracao->setDado( "cod_modulo", $inCodModulo                        );
            $obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio()              );
            $obTAdministracaoConfiguracao->setDado( "parametro" , "cnae_fiscal".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["HdninCodCnae"]              );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "centralizacao".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["inCodCentralizacao"]          );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "codigo_outras_entidades_sefip".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["inCodigoOutrasEntidades"]                     );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "fpas".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["inCodFPAS"]          );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "gps".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["inCodPagamentoGPS"] );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "tipo_inscricao".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["inTipoInscricao"]              );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "inscricao_fornecedor".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["inCGM"]                              );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "nome_pessoa_contato_sefip".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["stPessoaContato"]                         );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "telefone_pessoa_contato_sefip".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["stTelefoneContato"]                           );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "DDD_pessoa_contato_sefip".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["stDDDContato"]                           );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obTAdministracaoConfiguracao->setDado( "parametro" , "mail_pessoa_contato_sefip".Sessao::getEntidade() );
            $obTAdministracaoConfiguracao->setDado( "valor"     , $_POST["stEmailContato"]                          );
            $obTAdministracaoConfiguracao->alteracao($boTransacao);

            $obErro = $obTIMACategoriaSefip->excluirTodos($boTransacao);

            if (!$obErro->ocorreu()) {
                foreach (Sessao::read("arModalidades") as $inIndex=>$arModalidade) {
                    $obTIMACategoriaSefip->setDado("cod_modalidade",$arModalidade["inCodModalidadeRecolhimento"]);

                    foreach ($arModalidade["categorias"] as $inCodCategoria) {
                        $obTIMACategoriaSefip->setDado("cod_categoria",$inCodCategoria);
                        $obErro = $obTIMACategoriaSefip->inclusao($boTransacao);

                        if ($obErro->ocorreu()) {
                            break;
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                $stMsg = "Configuração da SEFIP concluída com sucesso!";

                Sessao::write('NOVAacao',$stAcao);

                SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=".$stAcao, $stMsg."",$stAcao,"aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'form', 'erro', Sessao::getId(), '../');
            }
        }
    break;
}

Sessao::encerraExcecao();

SistemaLegado::LiberaFrames(true,true);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
