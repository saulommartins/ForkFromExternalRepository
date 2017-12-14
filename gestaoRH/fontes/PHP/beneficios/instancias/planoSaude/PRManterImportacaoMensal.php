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
    * Página de processamento para baixa de debito automatica
    * Data de criação : 24/03/2006

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo B. Paulino

    * $Id: PRManterBaixaAutomatica.php 46941 2012-06-29 11:36:31Z tonismar $

    Caso de uso: uc-05.03.10
**/

/*
$Log$
Revision 1.15  2007/08/08 15:16:23  cercato
Bug#9853#

Revision 1.14  2007/06/06 21:24:37  dibueno
Bug #9371#

Revision 1.13  2007/04/05 19:00:00  dibueno
Bug #9027#

Revision 1.12  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CLA_ARQUIVO_ZIP);
include_once(CAM_GRH_BEN_MAPEAMENTO."TBeneficioLayoutFornecedor.class.php");

$stAcao = $request->get('stAcao');

$stPrograma = "ManterImportacaoMensal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";   
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro;
$arquivo = fopen($_FILES['stArquivo']['tmp_name'], "rb");
    
if ($arquivo == false) {
    SistemaLegado::exibeAviso(urlencode("Não foi possível abrir o arquivo."),"n_erro","erro",Sessao::getId(), "../" );
    sistemaLegado::LiberaFrames();
    die;
}

$csv_mimetypes = array(
    'text/csv',    
    'application/csv',
    'text/comma-separated-values',
    'application/excel',
    'application/vnd.ms-excel',
    'application/vnd.msexcel'
);
//Verificas os varios modos de como o sistema identifica um arquivo CSV e valida se o formato esta correto
if ( !in_array($_FILES['stArquivo']['type'],$csv_mimetypes) ) {
    SistemaLegado::exibeAviso(urlencode("Extensão de arquivo não permitida. Por favor, selecione o arquivo em formato csv."),"n_erro","erro",Sessao::getId(), "../" );
    sistemaLegado::LiberaFrames();
    die;
}

$obTBeneficioLayoutFornecedor = new TBeneficioLayoutFornecedor();
$obTBeneficioLayoutFornecedor->recuperaTodos($rsBeneficioLayoutPlanoSaude, " WHERE cgm_fornecedor = ".$request->get('inCGMFornecedor')."");

switch ($rsBeneficioLayoutPlanoSaude->getCampo('cod_layout')) {
    case 1: //UNIMED
        
        include_once(CAM_GRH_BEN_INSTANCIAS."planoSaude/LayoutArquivoUnimed.php");
        $obLayoutArquivoUnimed = new LayoutArquivoUnimed();
        
        $obErro = $obLayoutArquivoUnimed->RecuperaLayoutArquivoUnimed($arquivo);
        
    break;
}

$arquivoDownload = Sessao::read('arquivo_download');

if ( !$obErro->ocorreu() ) {
    SistemaLegado::ExecutaFrameOculto("window.open('DWManterImportacaoMensal.php?stAcao=download&arquivo=".$arquivoDownload."','','width=500,height=300');");
    SistemaLegado::alertaAviso($pgForm, "Arquivo: RelatorioImportacao","incluir","aviso", Sessao::getId(), "../" );
} else {
    SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
}

?>