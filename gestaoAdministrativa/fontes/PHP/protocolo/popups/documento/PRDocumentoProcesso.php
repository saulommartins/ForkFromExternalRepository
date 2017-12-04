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
* Popup para inclusão de arquivos anexos aos processo
* Data de Criação: 17/10/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 17525 $
$Name$
$Author: cassiano $
$Date: 2006-11-09 13:44:15 -0200 (Qui, 09 Nov 2006) $

Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TPROCopiaDigital.class.php";

$stPrograma = "DocumentoProcesso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once($pgJs);

$inCodProcesso  = $request->get('inCodProcesso'); 
$stAnoProcesso  = $request->get('stAnoProcesso');
$inCodDocumento = $request->get('inCodDocumento'); 

Sessao::write('nom_arquivo', $_FILES['stArquivo']['name']);

if ($_FILES['stArquivo']['type'] != 'image/jpeg' and $_POST['boImagem'] == 't') {
    SistemaLegado::exibeAviso("O Arquivo precisa ser estar no formato JPG!","","erro");
} elseif ($_FILES['stArquivo']['size'] > 1000000) {
    SistemaLegado::exibeAviso("O Arquivo não pode ter mais que 1000KB","","erro");
} else {

    $stDirUpload = CAM_PROTOCOLO."tmp/";
    $stDirAnexo  = CAM_PROTOCOLO."anexos/";

    # Cria o diretório caso não exista
    if ( !is_dir($stDirUpload) ) {
        mkdir($stDirUpload, 0755);
    }

    # Cria o diretório de Anexo caso não exista
    if ( !is_dir($stDirAnexo) ) {
        mkdir($stDirAnexo, 0755);
    }

    $obTPROCopiaDigital = new TPROCopiaDigital();
    $obTPROCopiaDigital->setDado('cod_documento' , $inCodDocumento);
    $obTPROCopiaDigital->setDado('cod_processo'  , $inCodProcesso);
    $obTPROCopiaDigital->setDado('exercicio'     , $stAnoProcesso);
    $obTPROCopiaDigital->proximoCod($inCodCopia);
   
    # Nome do arquivo formatado para ser único
    $stNomeArquivo = $inCodCopia.'_'.$inCodDocumento.'_'.$inCodProcesso.'_'.$stAnoProcesso.'_'.$_FILES['stArquivo']['name'];

    if ( !is_file( $stDirUpload."/".$stNomeArquivo ) ) {

        $boCopia = copy( $_FILES['stArquivo']['tmp_name'], $stDirUpload."/".$stNomeArquivo );
        chmod($stDirUpload."/".$stNomeArquivo,0777);
        
        if ($boCopia) {
        
            # Copia o arquivo para o diretório Anexo
            $boCopiaAnexo = copy( $_FILES['stArquivo']['tmp_name'], $stDirAnexo."/".$stNomeArquivo );
            chmod($stDirAnexo."/".$stNomeArquivo,0777);    
            
            if ($_FILES['stArquivo']['name'] != "." && $_FILES['stArquivo']['name'] != "..") {
                $stExtencao = substr($_FILES['stArquivo']['name'] , strrpos($_FILES['stArquivo']['name'],'.') );

                if (strtolower($stExtencao) == '.jpg' || strtolower($stExtencao) == '.jpeg') {
                    $boImagem = 't';
                } else {
                    $boImagem = 'f';
                }

                $obTPROCopiaDigital->setDado('cod_documento' , $inCodDocumento);
                $obTPROCopiaDigital->setDado('cod_processo'  , $inCodProcesso);
                $obTPROCopiaDigital->setDado('exercicio'     , $stAnoProcesso);
                $obTPROCopiaDigital->setDado('cod_copia'     , $inCodCopia);
                $obTPROCopiaDigital->setDado('imagem'        , $boImagem);
                $obTPROCopiaDigital->setDado('anexo'         , $stNomeArquivo);
                $obTPROCopiaDigital->inclusao();
            }

            SistemaLegado::exibeAvisoTelaPrincipal("Arquivo enviado com sucesso!","","");
            
            $stJs = "<script>montaParametrosGET('montaListaAnexos');</script>";
            echo ($stJs);            
        } else {
            SistemaLegado::exibeAviso("Erro no upload de arquivo!","","erro");
        }
    } else {
        SistemaLegado::exibeAviso("O arquivo enviado já existe no servidor, renomeie o arquivo e envie novamente!","","erro");
    }
}

?>
