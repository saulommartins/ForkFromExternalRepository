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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 28994 $
    $Name$
    $Author: luiz $
    $Date: 2008-04-04 10:43:45 -0300 (Sex, 04 Abr 2008) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.1  2007/10/17 13:41:48  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioApolice.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioApoliceBem.class.php");

$stPrograma = "ManterApolice";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioApolice = new TPatrimonioApolice();
Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioApolice );

switch ($stAcao) {
    case 'incluir' :
            // verifica se a data e valida
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtVencimento']))) < date('Ymd') ) {
                $stMensagem = 'A data de validade da apólice deve ser igual ou superior a data de hoje';
            }

            // verifica se a data de início da vigencia é maior ou igual que a data de vencimento
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioVigencia']))) >= implode('',array_reverse(explode('/',$_REQUEST['dtVencimento']))) ) {
                $stMensagem = 'A data de vencimento da apólice deve ser superior a data de início da vigência';
            }

            // verifica se nao existe uma apolice ja cadastrada no banco
            $stFiltro = "
                WHERE numcgm = ".$_REQUEST['inNumCGM']."
                  AND num_apolice = '".$_REQUEST['stNumApolice']."'
            ";
            $obTPatrimonioApolice->recuperaTodos( $rsSeguradoras, $stFiltro );
            if ( $rsSeguradoras->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe uma apólice com este número para esta seguradora';
            }

            if ( is_uploaded_file( $_FILES['stArquivo']['tmp_name'] ) ) {
                $stNomeArquivo = sistemaLegado::getmicrotime().'_'.$_FILES['stArquivo']['name'];
                $stCaminhoTmp   = CAM_GP_PAT_ANEXOS.'apolice/';

                if ( file_exists( $stCaminhoTmp.$stNomeArquivo  ) ) {
                    $stMensagem = "Arquivo já existente, informe um arquivo com outro nome.";
                } else {
                    if ( is_writeable( $stCaminhoTmp ) ) {
                        $boMoveArquivo = move_uploaded_file( $_FILES['stArquivo']['tmp_name'], $stCaminhoTmp.$stNomeArquivo );
                        if (!$boMoveArquivo) {
                            $stMensagem = "Erro ao gravar o arquivo. Consultar o adminstrador do sistema para veririfcar permissão de escrita no .";
                        } else {
                            $obTPatrimonioApolice->setDado( 'nome_arquivo', $stNomeArquivo );
                        }
                    } else {
                        $stMensagem = "O diretório ".$stCaminhoTmp." não tem permissão de escrita. Contate o administrador.";
                    }
                }
            }

            if (!$stMensagem) {
                //realiza a inclusao
                $obTPatrimonioApolice->proximoCod( $inCodApolice );
                $obTPatrimonioApolice->setDado( 'cod_apolice', $inCodApolice );
                $obTPatrimonioApolice->setDado( 'numcgm', $_REQUEST['inNumCGM'] );
                $obTPatrimonioApolice->setDado( 'num_apolice', $_REQUEST['stNumApolice'] );
                $obTPatrimonioApolice->setDado( 'dt_vencimento', $_REQUEST['dtVencimento'] );
                $obTPatrimonioApolice->setDado( 'contato', $_REQUEST['stContato'] );
                $obTPatrimonioApolice->setDado( 'dt_assinatura', $_REQUEST['dtAssinatura'] );
                $obTPatrimonioApolice->setDado( 'inicio_vigencia', $_REQUEST['dtInicioVigencia'] );
                $obTPatrimonioApolice->setDado( 'valor_apolice', $_REQUEST['nuValor'] );
                $obTPatrimonioApolice->setDado( 'valor_franquia', $_REQUEST['nuValorFranquia'] );
                $obTPatrimonioApolice->setDado( 'observacoes', $_REQUEST['stObservacoes'] );
                $obTPatrimonioApolice->inclusao();
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Apólice - ".$_REQUEST['stNumApolice'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            }

        break;
    case 'alterar' :
            // verifica se a data e valida
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtVencimento']))) < date('Ymd') ) {
                $stMensagem = 'A data de validade da apólice deve ser igual ou superior a data de hoje';
            }

            // verifica se a data de início da vigencia é maior ou igual que a data de vencimento
            if ( implode('',array_reverse(explode('/',$_REQUEST['dtInicioVigencia']))) >= implode('',array_reverse(explode('/',$_REQUEST['dtVencimento']))) ) {
                $stMensagem = 'A data de vencimento da apólice deve ser superior a data de início da vigência';
            }

            //se o num_apolice do banco e diferente do alterado, verifica
            if ($_REQUEST['stNumApolice'] != $_REQUEST['hdnNumApolice']) {
                // verifica se nao existe uma apolice ja cadastrada no banco
                $stFiltro = "
                    WHERE numcgm = ".$_REQUEST['inNumCGM']."
                      AND num_apolice = '".$_REQUEST['stNumApolice']."'
                ";
                $obTPatrimonioApolice->recuperaTodos( $rsSeguradoras, $stFiltro );
                if ( $rsSeguradoras->getNumLinhas() > 0 ) {
                    $stMensagem = 'Já existe uma apólice com este número para esta seguradora';
                }
            }

            if ( is_uploaded_file( $_FILES['stArquivo']['tmp_name'] ) ) {
                $stNomeArquivo = sistemaLegado::getmicrotime().'_'.$_FILES['stArquivo']['name'];
                $stCaminhoTmp   = CAM_GP_PAT_ANEXOS.'apolice/';

                if ( file_exists( $stCaminhoTmp.$stNomeArquivo  ) ) {
                    $stMensagem = "Arquivo já existente, informe um arquivo com outro nome.";
                } else {
                    if ( is_writeable( $stCaminhoTmp ) ) {
                        $boMoveArquivo = move_uploaded_file( $_FILES['stArquivo']['tmp_name'], $stCaminhoTmp.$stNomeArquivo );
                        if (!$boMoveArquivo) {
                            $stMensagem = "Erro ao gravar o arquivo. Consultar o adminstrador do sistema para veririfcar permissão de escrita no .";
                        } else {
                            $obTPatrimonioApolice->setDado( 'nome_arquivo', $stNomeArquivo );
                        }
                    } else {
                        $stMensagem = "O diretório ".$stCaminhoTmp." não tem permissão de escrita. Contate o administrador.";
                    }
                }
            }

            if (!$stMensagem) {
                //realiza a inclusao
                $obTPatrimonioApolice->setDado( 'cod_apolice', $_REQUEST['inCodApolice'] );
                $obTPatrimonioApolice->setDado( 'numcgm', $_REQUEST['inNumCGM'] );
                $obTPatrimonioApolice->setDado( 'num_apolice', $_REQUEST['stNumApolice'] );
                $obTPatrimonioApolice->setDado( 'dt_vencimento', $_REQUEST['dtVencimento'] );
                $obTPatrimonioApolice->setDado( 'contato', $_REQUEST['stContato'] );
                $obTPatrimonioApolice->setDado( 'dt_assinatura', $_REQUEST['dtAssinatura'] );
                $obTPatrimonioApolice->setDado( 'inicio_vigencia', $_REQUEST['dtInicioVigencia'] );
                $obTPatrimonioApolice->setDado( 'valor_apolice', $_REQUEST['nuValor'] );
                $obTPatrimonioApolice->setDado( 'valor_franquia', $_REQUEST['nuValorFranquia'] );
                $obTPatrimonioApolice->setDado( 'observacoes', $_REQUEST['stObservacoes'] );
                $obTPatrimonioApolice->alteracao();
                SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Apólice - ".$_REQUEST['stNumApolice'],"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            }

        break;
    case 'excluir' :

        //seta os dados e executa a exclusao
        $obTPatrimonioApolice->setDado( 'cod_apolice', $_REQUEST['inCodApolice'] );
        $obTPatrimonioApolice->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Apólice - ".$_REQUEST['inCodApolice'],"excluir","aviso", Sessao::getId(), "../");

        break;

}
Sessao::encerraExcecao();
