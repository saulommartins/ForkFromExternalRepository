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
 * Página de processamento para o cadastro de fotos do imóvel
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * @author     Desenvolvedor Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * $Id:$
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovelFoto";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch ($_REQUEST['stAcao']) {
    case 'incluir':
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelFoto.class.php");
        if (count($_FILES) > 0 ) {
            $obErro = new Erro;
            Sessao::setTrataExcecao( true );
            $obTCIMImovelFoto = new TCIMImovelFoto();
            Sessao::getTransacao()->setMapeamento( $obTCIMImovelFoto );
            $arFile = $_FILES['stArquivo'];
            $inCont =count($arFile[name]);
            $inErro = 0;
            $inQtdImagensSalvas = 0;
            for ($inI=0; $inI < $inCont ;$inI++) {
                if ($arFile['name'][$inI] != '') {
                    if ($arFile['error'][$inI] > 0) {
                        $inErro = 1;
                        $obErro->setDescricao('Ocorreu algum erro salvando o arquivo '.$arFile['name'][$inI].'!');
                    } else {
                        if ($arFile['size'][$inI] > 2048000) {
                            $inErro = 1;
                            $obErro->setDescricao('O arquivo '.$arFile['name'][$inI].' &eacute; maior que os 2 megas permitido!');
                        } elseif ($arFile['type'][$inI]!='image/jpeg' and $arFile['type'][$inI]!='image/png') {
                            $inErro = 1;
                            $obErro->setDescricao('O formato arquivo '.$arFile['name'][$inI].' &eacute; jpg/jpeg!');
                        } else {
                            $obTCIMImovelFoto->setDado('inscricao_municipal',$_REQUEST['inInscricaoMunicipal']);
                            $obErro = $obTCIMImovelFoto->proximoCod($inCodFoto);
                            if ( !$obErro->ocorreu() ) {
                                $inQtdImagensSalvas++;
                                $inTamanhoArquivo = $arFile['size'][$inI];
                                $fp = fopen($arFile['tmp_name'][$inI], "rb");
                                $resArquivoTemp = fread($fp, $inTamanhoArquivo);
                                fclose($fp);
                                $obTCIMImovelFoto->setDado('cod_foto',$inCodFoto);
                                $obTCIMImovelFoto->setDado('descricao',$_REQUEST['stDescricao'][$inI]);
                                $obTCIMImovelFoto->setDado('foto',$resArquivoTemp);
                                $obErro = $obTCIMImovelFoto->inclusao();
                            }
                        }
                    }
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }
            Sessao::encerraExcecao();
            if ( !$obErro->ocorreu() and $inQtdImagensSalvas > 0 ) {
                $pgForm.="?inInscricaoMunicipal=".$_REQUEST['inInscricaoMunicipal'];
                SistemaLegado::alertaAviso($pgForm,urlencode("N&uacute;mero da inscri&ccedil;&atilde;o: ".$_REQUEST['inInscricaoMunicipal']),"incluir","aviso");
            } elseif ($obErro->ocorreu()) {
                SWITCH($inErro){
                    case 1:
                        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                    break;
                    default:
                        SistemaLegado::exibeAviso(urlencode($_FILES['stArquivo'][name][$inI]),"n_incluir","erro");
                    break;
                }

            }
        }
    break;
    case 'excluir':
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelFoto.class.php");
        Sessao::setTrataExcecao( true );
        $obTCIMImovelFoto = new TCIMImovelFoto();
        Sessao::getTransacao()->setMapeamento( $obTCIMImovelFoto );
        $obTCIMImovelFoto->setDado('inscricao_municipal',$_REQUEST['inInscricaoMunicipal']);
        $obTCIMImovelFoto->setDado('cod_foto',$_REQUEST['inCodFoto']);
        $obErro = $obTCIMImovelFoto->exclusao();
        Sessao::encerraExcecao();
        if ( !$obErro->ocorreu() ) {
            $pgForm.='?inInscricaoMunicipal='.$_REQUEST['inInscricaoMunicipal'].'&stValorLote='.$_REQUEST['stValorLote'];
             SistemaLegado::alertaAviso($pgForm,urlencode("Excluir imagem conclu&iacute;do com sucesso!(N&uacute;mero da inscri&ccedil;&atilde;o: ".$_REQUEST['inInscricaoMunicipal'].",Foto:".$_REQUEST['inCodFoto'].")" ),"unica","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($_FILES['stArquivo'][name][$inI]),"n_excluir","erro");
        }
    break;

}
?>
