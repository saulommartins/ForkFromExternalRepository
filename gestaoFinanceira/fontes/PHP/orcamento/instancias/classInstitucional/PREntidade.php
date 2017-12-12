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
    * Página de Formulario de Inclusao/Alteracao de Entidade
    * Data de Criação   : 15/07/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-07-24 18:43:41 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.19  2007/07/24 21:43:41  hboaventura
Bug#9748#

Revision 1.18  2007/07/03 21:32:48  bruce
Bug #9552# , Bug #9534#

Revision 1.17  2006/11/17 18:48:16  cako
Bug #7441#

Revision 1.16  2006/11/17 17:56:22  cako
Bug #7441#

Revision 1.15  2006/07/05 20:42:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Entidade";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obREntidadeOrcamento  = new ROrcamentoEntidade;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $obErro = new Erro;
        foreach ($_REQUEST as $key => $valor) {
            if (!$obErro->ocorreu()) {
                if (substr($key,0,10)=="boEntidade") {
                    list($boEntidade,$exercicio,$cod_entidade,$numcgm,$cod_responsavel,$cod_resp_tecnico,$cod_profissao) = explode("|",$key);
                    $obREntidadeOrcamento->setCodigoEntidade           ( $cod_entidade       );
                    $stCodEntidades .= $cod_entidade.",";
                    $obREntidadeOrcamento->setExercicio                ( $exercicio          );
                    $obREntidadeOrcamento->setNumCGM                   ( $numcgm             );
                    $obREntidadeOrcamento->setCodigoResponsavel        ( $cod_responsavel    );
                    $obREntidadeOrcamento->setCodigoResponsavelTecnico ( $cod_resp_tecnico   );
                    $obREntidadeOrcamento->setCodigoProfissao          ( $cod_profissao      );
                    $obREntidadeOrcamento->listarUsuariosPermitidos    ( $rsRecordSet );
                    $obREntidadeOrcamento->setUsuarios( "" );
                    while (!$rsRecordSet->eof()) {
                        $obREntidadeOrcamento->addUsuario();
                        $obREntidadeOrcamento->obUltimoUsuario->setNumCGM( $rsRecordSet->getCampo('numcgm') );
                        $obREntidadeOrcamento->commitUsuario();

                        $rsRecordSet->proximo();
                    }
                    $obREntidadeOrcamento->setExercicio                ( Sessao::getExercicio() );
                    $obErro = $obREntidadeOrcamento->incluir();
                    $boAtivar = true;
                }
            }
        }
        if ($stCodEntidades) {
            if ( !$obErro->ocorreu() ) {
                $stCodEntidades = substr($stCodEntidades,0,strlen($stCodEntidades)-1);
                if(strlen($stCodEntidades) <= 2)
                    $stEntidades = "Entidade: ".$stCodEntidades;
                else $stEntidades = "Entidades: ".$stCodEntidades;
                SistemaLegado::alertaAviso($pgForm,$stEntidades,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }

        $arItens = Sessao::read('arItens');
        if (count($arItens)>0) {

            if(!$obErro) $obErro = new Erro;
            $stEntidades = '';
            foreach ($arItens as $k => $v) {
                if (!$obErro->ocorreu()) {
                   $obREntidadeOrcamento->setCodigoEntidade           ( $v['inCodigoEntidade'] );
                   $stCodEntidades .= $v['inCodigoEntidade'].",";
                   $obREntidadeOrcamento->setNumCGM                   ( $v['inNumCGM']                   );
                   $obREntidadeOrcamento->setCodigoResponsavel        ( $v['inCodigoResposavel']         );
                   $obREntidadeOrcamento->setCodigoResponsavelTecnico ( $v['inCodigoResponsavelTecnico'] );
                   $obREntidadeOrcamento->setCodigoProfissao          ( $v['inCodProfissao']             );
                   $obREntidadeOrcamento->setExercicio                ( Sessao::getExercicio()               );
                   $obREntidadeOrcamento->setArquivoLogotipo          ( $v['stNomeArquivo']              );
                   if (is_array($v['inCodigoUsuariosSelecionados'] ) ) {
                       $obREntidadeOrcamento->setUsuarios(null);
                       foreach ($v['inCodigoUsuariosSelecionados'] as $chave => $valor) {
                           $obREntidadeOrcamento->addUsuario();
                           $obREntidadeOrcamento->obUltimoUsuario->setNumCGM( $valor );
                           $obREntidadeOrcamento->commitUsuario();
                       }
                   }
                   $obErro = $obREntidadeOrcamento->incluir();
                   $boIncluir = true;
                }
            }

            if ( !$obErro->ocorreu() ) {
                $stCodEntidades = substr($stCodEntidades,0,strlen($stCodEntidades)-1);
                if(strlen($stCodEntidades) <= 2)
                    $stEntidades = "Entidade: ".$stCodEntidades;
                else $stEntidades = "Entidades: ".$stCodEntidades;
                SistemaLegado::alertaAviso($pgForm,$stEntidades,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }

        if(!$boAtivar && !$boIncluir)
                SistemaLegado::exibeAviso("Você deve incluir ao menos uma entidade na lista ou selecionar uma para ser ativada","n_incluir","aviso");

    break;

    case "alterar":
        $obErro = new Erro;
        //seta array de Usuarios
        foreach ($_POST['inCodigoUsuariosSelecionados'] as $key => $valor) {
            $obREntidadeOrcamento->addUsuario();
            $obREntidadeOrcamento->obUltimoUsuario->setNumCGM( $valor );
            $obREntidadeOrcamento->commitUsuario();
        }
        $obREntidadeOrcamento->setExercicio                ( Sessao::getExercicio()                   );
        $obREntidadeOrcamento->setCodigoEntidade           ( $_POST['inCodigoEntidade']           );
        $obREntidadeOrcamento->setNumCGM                   ( $_POST['inNumCGM']                   );
        $obREntidadeOrcamento->setCodigoResponsavel        ( $_POST['inCodigoResponsavel']        );
        $obREntidadeOrcamento->setCodigoResponsavelTecnico ( $_POST['inCodigoResponsavelTecnico'] );
        $obREntidadeOrcamento->setCodigoProfissao          ( $_POST['inCodProfissao']             );
        if ($_FILES['stArquivoLogotipo']['name'] and $_FILES['stArquivoLogotipo']['name'] != $_POST['stHdnArquivoLogotipo']) {
            if ( is_uploaded_file( $_FILES['stArquivoLogotipo']['tmp_name'] ) ) {
                $stNomeArquivo  = 'imgBrs'.$_FILES['stArquivoLogotipo']['name'];
                $stCaminhoAnexo = CAM_GF_ORCAMENTO.'anexos/';
                $stCaminhoTmp   = CAM_GF_ORCAMENTO.'tmp/';
                if ( file_exists( $stCaminhoAnexo.$stNomeArquivo  ) ) {
                    $stErro = "Arquivo já existente, informe um arquivo com outro nome.";
                } else {
                    if ( strpos( $_FILES['stArquivoLogotipo']['type'], 'image/jpeg' ) === false ) {
                        $stErro = "Formato de arquivo inválido: Utilize somente imagens do tipo .JPG";
                    } else {
                        if ( is_writeable( $stCaminhoTmp ) ) {
                            $boMoveArquivo = move_uploaded_file( $_FILES['stArquivoLogotipo']['tmp_name'], $stCaminhoTmp.$stNomeArquivo );
                            if (!$boMoveArquivo) {
                                $stErro = "Erro ao gravar o arquivo. Consultar o adminstrador do sistema para veririfcar permissão de escrita no .";
                            } else {
                                $obREntidadeOrcamento->setArquivoLogotipo( $stNomeArquivo );
                            }
                        } else {
                            $stErro = "O diretório ".$stCaminhoTmp." não tem permissão de escrita. Contate o administrador.";
                        }
                    }
                }
            } else {
                $stErro = "Arquivo de upload inválido.";
            }
        } elseif ($_POST['boApargarLogotipo'] != '') {
            $obREntidadeOrcamento->setArquivoLogotipo( true );
        } else {
            $obREntidadeOrcamento->setArquivoLogotipo( false );
        }
        if ($stErro) {
            $obErro->setDescricao( $stErro );
        }

        if (  !$obErro->ocorreu() ) {
            $obErro = $obREntidadeOrcamento->alterar();

            $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro['filtro'] as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

            if ( !$obErro->ocorreu() ) {

                if ($_POST['boDesativarEntidade']==1) {

    //                $obREntidadeOrcamento->setCodigoEntidade    ( $_POST['inCodigoEntidade'] );
    //                $obREntidadeOrcamento->setExercicio         ( Sessao::getExercicio()         );
                      $obErro = $obREntidadeOrcamento->excluir();

                      if ( !$obErro->ocorreu() ) {
                          SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Entidade: ".$_POST['inCodigoEntidade'],"alterar","aviso", Sessao::getId(), "../");
                      } else {
                          SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                      }
                } else {
                    SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Entidade: ".$_POST['inCodigoEntidade'],"alterar","aviso", Sessao::getId(), "../");
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}

?>
