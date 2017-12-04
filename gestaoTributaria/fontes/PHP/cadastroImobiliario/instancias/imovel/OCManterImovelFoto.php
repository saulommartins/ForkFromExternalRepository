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
 * Cadastro de fotos para imóveis já cadastrados no sistema
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * @author     Desenvolvedor Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSBuscaLote.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
switch ($_REQUEST['stCtrl']) {
case 'montaFileBox':
    $obFormulario = new Formulario;

    for ($inI=1;$inI<=$_REQUEST[inNumeroFoto];$inI++) {
        $obFileBox = new FileBox();
        $obFileBox->setId("stArquivo$inI");
        $obFileBox->setName("stArquivo[]");
        $obFileBox->setTitle("Selecione uma foto no formato jpg para o imóvel selecionado com no máximo 2 megas de tamanho de arquivo.");
        $obFileBox->setRotulo("Foto ".$inI);

        $obTxtDescricao = new TextBox;
        $obTxtDescricao->setName('stDescricao[]');
        $obTxtDescricao->setRotulo("Descri&ccedil;&atilde;o da foto ".$inI);
        $obTxtDescricao->setSize(30);
        $obTxtDescricao->setMaxLength(30);

        $obFormulario->addComponente($obFileBox);
        $obFormulario->addComponente($obTxtDescricao);
    }
    $obFormulario->montaInnerHtml();
    echo $obFormulario->getHTML();
    if ($_REQUEST[inNumeroFoto]) {
?>
    <script type='text/javascript'>
        jQuery('#stArquivo1').focus();
    </script>
<?php
    }
break;
case 'carregaImagem':
    include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelFoto.class.php");

    $obImageBox=Sessao::read('obImageBox');
    $obTCIMImovelFoto = new TCIMImovelFoto();
    $obTCIMImovelFoto->setDado('cod_foto',$_REQUEST['inCodFoto']);
    $obTCIMImovelFoto->setDado('inscricao_municipal',$_REQUEST['inInscricaoMunicipal']);

    Sessao::setTrataExcecao( true );
    Sessao::getTransacao()->setMapeamento( $obTCIMImovelFoto );
    header('Content-type: image/jpg');
    $obTCIMImovelFoto->recuperaFoto($stImagem);
    $obImageBox->ajustaTamanhoImagem( $stImagem,$_REQUEST['boBox']);

    Sessao::encerraExcecao();
break;
}

?>
