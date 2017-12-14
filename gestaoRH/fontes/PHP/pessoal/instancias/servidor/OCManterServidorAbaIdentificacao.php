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
    * Página processamento ocuto Pessoal ServidorP
    * Data de Criação   : 14/12/2004
    *

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 30862 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.07
*/

include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'    );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php' );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma          = "ManterServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJS                = "JS".$stPrograma.".js";
$stJs                = "";

function incluirFoto()
{
    if ($_FILES['stCaminhoFoto']<>"") {
        $tam = getimagesize($_FILES['stCaminhoFoto']['tmp_name']);
        $tam_x = $tam[0];
        $tam_y = $tam[1];
        $tam_type = $tam['mime'];

        if ( ($tam_type <> "image/jpeg") && ($tam_type <> "image/png") && ($tam_type <> "image/gif") ) {
            $stMensagem = urlencode("O arquivo inserido deve ter o formato JPG, PNG ou GIF.");
            $stJs .= "alertaAviso('".$stMensagem."', 'form', 'erro', '".Sessao::getId()."');";
        } elseif ($stCaminhoFoto_size>"307200") {
            $stMensagem = urlencode("O arquivo inserido deve ter o tamanho máximo de 300 Kb.");
            $stJs .= "alertaAviso('".$stMensagem."', 'form', 'erro', '".Sessao::getId()."');";
        } else {
            //DEFINE OS PARÂMETROS DA MINIATURA
            if ($tam_x > 85 || $tam_y > 112) {
                if ( ($tam_x-85)/3 > ($tam_y - 112)/4 ) {
                    //DIMINUI PELO X
                    $porcentagem = 85 / $tam_x;
                    $largura = round($tam_x * $porcentagem);
                    $altura = round($tam_y * $porcentagem);
                } else {
                    //DIMINUI PELO Y
                    $porcentagem = 112 / $tam_y;
                    $largura = round($tam_x * $porcentagem);
                    $altura = round($tam_y * $porcentagem);
                }
            } else {
                $largura = $tam_x;
                $altura = $tam_y;
            }
            
            $uploaddir  = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/tmp/';
            $nome_foto  = date('YmdHisu') . session_id();
            $uploadfile = $uploaddir . $nome_foto;
            
            if (move_uploaded_file($_FILES['stCaminhoFoto']['tmp_name'], $uploadfile)) {
                $stJs .= "alertaAviso('Download efetuado com sucesso', 'form', 'erro', '".Sessao::getId()."');";
            } else {
                $stJs .= "alertaAviso('Erro ao efetuar download', 'form', 'erro', '".Sessao::getId()."');";
            }
            
            //$fhandler = fopen($_FILES['stCaminhoFoto']['tmp_name'],"r");
            //$stCaminhoFotoHandler = fread ($fhandler, $_FILES['stCaminhoFoto']['size']);
            //fclose($fhandler);
            #$_SESSION['FOTO_BIN'] = $stCaminhoFotoHandler;
            Sessao::write('FOTO_BIN' ,$stCaminhoFotoHandler);
            Sessao::write('FOTO_URL' , $uploadfile);
            Sessao::write('FOTO_NAME', $nome_foto);
            Sessao::write('FOTO_ARQ' ,$stCaminhoFoto);
            Sessao::write('FOTO_X'   ,$largura);
            Sessao::write('FOTO_Y'   ,$altura);

            $obImgFoto = new Img;
            $obImgFoto->setRotulo           ( "Foto"    );
            $obImgFoto->setId               ( "stFoto"  );
            $obImgFoto->setWidth            ( $largura  );
            $obImgFoto->setHeight           ( $altura  );
            $obImgFoto->setNull             ( true      );
            $obImgFoto->setCaminho          ( $uploadfile );

            $obFilFoto = new FileBox;
            $obFilFoto->setRotulo        ( "Caminho" );
            $obFilFoto->setName          ( "stCaminhoFoto" );
            $obFilFoto->setSize          ( 40 );
            $obFilFoto->setNull          ( true );
            $obFilFoto->setMaxLength     ( 100 );

            $obBtnFoto = new Button;
            $obBtnFoto->setName  ( "Incluir" );
            $obBtnFoto->setValue ( "Incluir" );
            $obBtnFoto->setStyle ( "width: 80px" );
            $obBtnFoto->obEvento->setOnClick("incluirFoto();");

            $obBtnFotoExcluir = new Button;
            $obBtnFotoExcluir->setName  ( "Remover" );
            $obBtnFotoExcluir->setValue ( "Remover" );
            $obBtnFotoExcluir->setStyle ( "width: 80px" );
            $obBtnFotoExcluir->obEvento->setOnClick("excluirFoto();");

            $obFormularioFoto = new Formulario;
            $obFormularioFoto->addComponente( $obImgFoto );
            $obFormularioFoto->agrupaComponentes( array($obFilFoto, $obBtnFoto, $obBtnFotoExcluir) );
            $obFormularioFoto->montaInnerHTML();

            $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnFoto').innerHTML = '".$obFormularioFoto->getHTML()."';";

        }
    } else {
        $stMensagem = urlencode("Arquivo não informado");
        $stJs .= "alertaAviso('".$stMensagem."', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirFoto()
{
    Sessao::write('FOTO_NAME',"no_foto.jpeg");

    $obImgFoto = new Img;
    $obImgFoto->setRotulo        ( "Foto"                                       );
    $obImgFoto->setTitle         ( "Foto."                                       );
    $obImgFoto->setId            ( "stFoto"                                     );
    $obImgFoto->setWidth         ( 60                                           );
    $obImgFoto->setHeight        ( 80                                           );
    $obImgFoto->setNull          ( true                                         );
    $obImgFoto->setCaminho       ( CAM_GRH_PES_ANEXOS."no_foto.jpeg"   );

    $obFilFoto = new FileBox;
    $obFilFoto->setRotulo        ( "Caminho"            );
    $obFilFoto->setName          ( "stCaminhoFoto"      );
    $obFilFoto->setSize          ( 40                   );
    $obFilFoto->setNull          ( true                 );
    $obFilFoto->setMaxLength     ( 100                  );

    $obBtnFoto = new Button;
    $obBtnFoto->setName             ( "Incluir"         );
    $obBtnFoto->setValue            ( "Incluir"         );
    $obBtnFoto->setStyle            ( "width: 80px"     );
    $obBtnFoto->obEvento->setOnClick( "incluirFoto();"  );

    $obFormularioFoto = new Formulario;
    $obFormularioFoto->addComponente        ( $obImgFoto                    );
    $obFormularioFoto->agrupaComponentes    ( array($obFilFoto, $obBtnFoto) );
    $obFormularioFoto->montaInnerHTML();

    $stJs .= "window.parent.frames['telaPrincipal'].document.getElementById('spnFoto').innerHTML = '".$obFormularioFoto->getHTML()."';";

    return $stJs;
}

function preencheMunicipioOrigem($inCodUF="",$inCodMunicipio="")
{
    $obRPessoalServidor = new RPessoalServidor;
    $stJs = isset($stJs) ? $stJs : null;
    $stJs .= "limpaSelect(f.stNomeMunicipio,0);                                 \n";
    $stJs .= "f.inCodMunicipio.value = '$inCodMunicipio';                       \n";
    $stJs .= "f.stNomeMunicipio[0] = new Option('Selecione','', 'selected');    \n";
    if ($_POST["inCodUF"] != "" or $inCodUF != "") {
        $obRPessoalServidor->recuperaTodosMunicipio( $rsMunicipioOrigem ,( $_POST["inCodUF"] ) ? $_POST["inCodUF"] : $inCodUF);
        $inContador = 1;
        $rsMunicipioOrigem->addFormatacao("nom_municipio","SLASHES");
        while ( !$rsMunicipioOrigem->eof() ) {
            $stJs .= "f.stNomeMunicipio.options[$inContador] = new Option('".$rsMunicipioOrigem->getCampo( "nom_municipio" )."','".$rsMunicipioOrigem->getCampo( "cod_municipio" )."'); \n";
            $inContador++;
            $rsMunicipioOrigem->proximo();
        }
    }
    $stJs .= "f.stNomeMunicipio.value = '$inCodMunicipio';                       \n";

    return $stJs;
}

function buscaCIDServidor(){
    global $request;
            
    $inSiglaCID = strtoupper($request->get('inSiglaCID'));
    $stDescricao = "&nbsp;";
    
    if(!empty($inSiglaCID)){
        $stFiltro = " WHERE sigla ILIKE '".$inSiglaCID."%' ";
        $obTPessoalCID = new TPessoalCID;
        $obTPessoalCID->recuperaTodos($rsCID, $stFiltro);
    
        if(count($rsCID->arElementos) > 0){
            $stDescricao = $rsCID->getCampo('descricao');
            $stJs .= "d.getElementById('inCodCID').value = '".$rsCID->getCampo('cod_cid')."'; \n";
        }else{
            $stDescricao = "&nbsp;";
            $stJs .= "d.getElementById('inSiglaCID').value = ''; \n";
            $stJs .= "alertaAviso('CID ".$inSiglaCID." não encontrado!','form','erro','".Sessao::getId()."'); \n";
        }
    }else{
        $stJs .= "d.getElementById('inCodCID').value = ''; \n";
    }
    $stJs .= " d.getElementById('stCID').innerHTML = '".$stDescricao."'; \n";
    
    return $stJs;
}

switch ($_POST["stCtrl"]) {
    case "incluirFoto":
        $stJs .= incluirFoto();
    break;
    case "excluirFoto":
        $stJs .= excluirFoto();
    break;
    case "preencheMunicipioOrigem":
        $stJs .= preencheMunicipioOrigem();
    break;
    case "buscaCIDServidor":
        $stJs .= buscaCIDServidor();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
