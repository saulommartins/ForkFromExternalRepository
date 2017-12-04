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
    * Pagina Oculta de Entidade
    * Data de Criação   : 04/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2007-07-03 18:35:23 -0300 (Ter, 03 Jul 2007) $

    * Casos de uso: uc-02.01.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "Entidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaListaDiverso($arRecordSet , $boExecuta = true)
{
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->ordena("inCodigoEntidade" , $stOrdem = "ASC", $stTipo = SORT_NUMERIC );
        if ( !$rsLista->eof() ) {
            $obLista = new Lista;
            $obLista->setTitulo("Entidades Incluídas");
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsLista );
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Código");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Nome da Entidade ");
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Exercício ");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "inCodigoEntidade" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stNomeCGM" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();
            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "exercicio" );
            $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
            $obLista->commitDado();

            $obLista->montaHTML();
            $stHTML = $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

            $stJS = "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n";
            $stJS .= "f.inNumCGM.value= \"\";\n";
            $stJS .= "d.getElementById('campoInner').innerHTML = \"&nbsp;\";\n";
            $stJS .= "f.inCodigoResponsavel.value = \"\";\n";
            $stJS .= "d.getElementById('campoInner2').innerHTML = \"&nbsp;\";\n";
            $stJS .= "f.inCodigoResponsavelTecnico.value = \"\";\n";
            $stJS .= "d.getElementById('campoInner3').innerHTML = \"&nbsp;\";\n";
            $stJS .= "passaItem(f.inCodigoUsuariosSelecionados,f.inCodigoUsuariosDisponiveis,'tudo');\n";
            $stJS .= "d.getElementById('CodigoEntidade').innerHTML= parseInt(f.inCodigoEntidade.value) + 1;\n";
            $stJS .= "f.inCodigoEntidade.value = trim(d.getElementById('CodigoEntidade').innerHTML);\n";
            SistemaLegado::executaFrameOculto( $stJS );
     }

}

switch ($stCtrl) {
    case 'incluiEntidade':
        $boGravarLista = true;
        $stErro = "";
        if ( is_uploaded_file( $_FILES['stArquivoLogotipo']['tmp_name'] ) ) {
            $boGravarLista = false;
            $stNomeArquivo  = 'imgBrs'.$_FILES['stArquivoLogotipo']['name'];
            $stCaminhoAnexo = CAM_GF_ORCAMENTO.'anexos/';
            $stCaminhoTmp   = CAM_GF_ORCAMENTO.'tmp/';
            if ( file_exists( $stCaminhoAnexo.$stNomeArquivo  ) ) {
                $stErro = "Arquivo já existente, informe um arquivo com outro nome.";
            } else {
                if ( strpos( $_FILES['stArquivoLogotipo']['type'], 'image' ) === false ) {
                    $stErro = "Formato de arquivo inválido.";
                } else {
                    if ( is_writeable( $stCaminhoTmp ) ) {
                        $boMoveArquivo = move_uploaded_file( $_FILES['stArquivoLogotipo']['tmp_name'], $stCaminhoTmp.$stNomeArquivo );
                        if (!$boMoveArquivo) {
                            $stErro = "Erro ao gravar o arquivo. Consultar o adminstrador do sistema para veririfcar permissão de escrita no .";
                        } else {
                            $boGravarLista = true;
                        }
                    } else {
                        $stErro = "O diretório ".$stCaminhoTmp." não tem permissão de escrita. Contate o administrador.";
                    }
                }
            }
        }

        $arItens = Sessao::read('arItens');
        if ($arItens) {
            foreach ($arItens as $arTmp => $arTmp2) {
                foreach ($arTmp2 as $arTmp => $arTmp2) {
                    foreach ($arTmp2 as $stCampo => $stValor) {
                        if ($stCampo == 'inNumCGM') {
                            if ($stValor == $_POST['inNumCGM']) {
                                $boCGMSelecionado = true;
                            }
                        }
                    }
                }
            }
        }

        if ($boCGMSelecionado) {
            $stErro = "Já existe entidade selecionada para o CGM ".$_POST['inNumCGM'];
            $boGravarLista = false;
        }

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"         );
        $obTOrcamentoEntidade = new TOrcamentoEntidade  ;
        $stFiltro = " where entidade.exercicio = '" . Sessao::getExercicio() .  "' and entidade.numcgm = ". $_POST['inNumCGM'] ;
        $obTOrcamentoEntidade->recuperaTodos ( $rsCGMOrcamento, $stFiltro );

        echo "Linhas.: " . $rsCGMOrcamento->getNumLinhas() ;

        if ( $rsCGMOrcamento->getNumLinhas() >0 ) {
            $stErro = "Já existe entidade cadastrada para o CGM ".$_POST['inNumCGM'];
            $boGravarLista = false;
        }

        if ($boGravarLista) {
            $inCount = sizeof($arItens);
            $arItens[$inCount]['num_item']                     = $inCount+1;
            $arItens[$inCount]['inCodigoEntidade']             = $_POST['inCodigoEntidade'];
            $arItens[$inCount]['inNumCGM']                     = $_POST['inNumCGM'];
            $arItens[$inCount]['stNomeCGM']                    = $_POST['campoInner'];
            $arItens[$inCount]['inCodigoResposavel']           = $_POST['inCodigoResponsavel'];
            $arItens[$inCount]['inCodigoResponsavelTecnico']   = $_POST['inCodigoResponsavelTecnico'];
            $arItens[$inCount]['inCodProfissao']               = $_POST['inCodProfissao'];
            $arItens[$inCount]['exercicio']                    = Sessao::getExercicio();
            $arItens[$inCount]['inCodigoUsuariosSelecionados']=$_REQUEST['inCodigoUsuariosSelecionados'];
            if ( isset( $stNomeArquivo ) ) {
                    $arItens[$inCount]['stNomeArquivo'] = $stNomeArquivo;
                    $arItens[$inCount]['logotipo'] = $stCaminhoTmp.$stNomeArquivo;
            }
        }
        Sessao::write('arItens',$arItens);
        if ($stErro) {
            SistemaLegado::exibeAviso(urlencode($stErro),"n_incluir","erro");
        } else {
            $stHTML = montaListaDiverso( $arItens);
        }
    break;
}

?>
