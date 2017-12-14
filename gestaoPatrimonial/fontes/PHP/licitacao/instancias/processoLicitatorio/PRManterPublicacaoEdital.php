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
    * Página de Processamento de cadastro de Fornecedores
    * Data de Criação   : 30/10/06

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fábio Moreira da Silva

    * @ignore
    * Casos de uso : uc-03.05.17

*/

/*
$Log$
Revision 1.14  2007/09/17 19:55:34  bruce
Ticket#10103#

Revision 1.13  2007/08/09 18:14:03  tonismar
inclusão do formulário de filtro antecipando o formulário de publicar edital

Revision 1.12  2007/08/09 14:33:41  tonismar
Bug#9850#

Revision 1.11  2007/08/07 15:48:49  hboaventura
Bug#9717#

Revision 1.10  2007/07/20 20:20:52  hboaventura
Bug#9717#

Revision 1.9  2007/03/02 20:03:52  hboaventura
bug #8579#, #8578#, #8575#

Revision 1.8  2007/02/23 17:03:47  hboaventura
Bug #8488#

Revision 1.7  2007/01/26 12:54:33  hboaventura
Bug #8159#

*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoPublicacaoEdital.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterPublicacaoEdital";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LSManterEdital.php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";
//controle de acao deste arquivo
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == '') {
    $stAcao = 'publicar';
}

//inicializar transação
Sessao::setTrataExcecao( true );
//instancia objeto de persistencia/mapeamento
$obTLicitacaoPublicacaoEdital = new TLicitacaoPublicacaoEdital();
Sessao::getTransacao()->setMapeamento( $obTLicitacaoPublicacaoEdital );

switch ($stAcao) {

  case 'publicar':
    $arVeiculos = Sessao::read('arVeiculos');
    $inTotalVeiculos = count($arVeiculos);
    if ($inTotalVeiculos <= 0) {
        sistemaLegado::exibeAviso( 'É preciso incluir ao menos um veículo de publicação.'   ,"n_incluir","erro");
    } else {
        $arEdital = explode('/',$_REQUEST['numEdital']);
        include_once( TLIC."TLicitacaoEdital.class.php" );
        $obTLicitacaoEdital = new TLicitacaoEdital();
        $obTLicitacaoEdital->setDado("num_edital",($arEdital[0]));
        $obTLicitacaoEdital->setDado("exercicio",$_REQUEST['exercicioEdital']);
        $obTLicitacaoEdital->recuperaPorChave( $rsPublicacaoEdital ) ;
        // acrescentada a validação do número do edital, ver Bug #9717# para mais detalhes
        if ( $rsPublicacaoEdital->getNumLinhas() < 0 ) {
            SistemaLegado::executaFrameOculto("alertaAviso('Número do Edital inválido!', 'form','','".Sessao::getId()."');");
            break;
        }

        //primeiramente exclui todos os registros relacionados ao edital
        $obTLicitacaoPublicacaoEdital->setDado("num_edital",($arEdital[0]));
        $obTLicitacaoPublicacaoEdital->setDado("exercicio", $_REQUEST['exercicioEdital']);
        $obTLicitacaoPublicacaoEdital->exclusao();
        for ($i=0; $i < $inTotalVeiculos; $i++) {
          $item = $arVeiculos[$i];
          $obTLicitacaoPublicacaoEdital->setDado("numcgm",(int) ($item['veiculoPublicacao']));
          $obTLicitacaoPublicacaoEdital->setDado("data_publicacao",addSlashes(trim($item['dataPublicacao'])));
          $obTLicitacaoPublicacaoEdital->setDado("observacao",html_entity_decode(trim($item['observacao'])));
          $obTLicitacaoPublicacaoEdital->setDado("num_publicacao", $item['inNumPublicacao']);
          $obTLicitacaoPublicacaoEdital->inclusao();
        }
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Publicação de Edital concluído com sucesso! (Edital ".$arEdital[0].")","aviso","aviso", Sessao::getId(), "../");
        break;
    }
}
Sessao::encerraExcecao();
?>
