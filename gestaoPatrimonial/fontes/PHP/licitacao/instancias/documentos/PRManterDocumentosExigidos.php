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
    * Página de Processamento dos Documentos
    * Data de Criação   : 22/05/2006

    * @author Analista: Gelson Gonsalves
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.05.12

*/

/*
$Log$
Revision 1.7  2007/02/23 11:57:06  hboaventura
Bug #8083#

Revision 1.6  2007/02/05 17:17:43  hboaventura
Bug #8083#

Revision 1.5  2007/01/15 18:08:23  rodrigo
#8083#

Revision 1.4  2007/01/15 17:45:46  rodrigo
#8083#

Revision 1.3  2006/11/30 09:59:31  larocca
Bug #7630#

Revision 1.2  2006/10/06 15:34:02  leandro.zis
correções

Revision 1.1  2006/10/06 13:31:00  leandro.zis
uc 03.05.12

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoDocumento.class.php"             );
include_once(TLIC."TLicitacaoLicitacaoDocumentos.class.php"   );
include_once(TLIC."TLicitacaoCertificacaoDocumentos.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumentosExigidos";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
$obTLicitacaoDocumento           = new TLicitacaoDocumento();

Sessao::getTransacao()->setMapeamento( $obTLicitacaoDocumento );

$stAcao = $request->get('stAcao');

switch ($stAcao) {

   case 'incluir':
        $obTLicitacaoDocumento->setDado("nom_documento",$_REQUEST['stNomeDocumento']);
        if (SistemaLegado::pegaDado("nom_documento","licitacao.documento","Where nom_documento = '".$obTLicitacaoDocumento->getDado("nom_documento")."' ")) {
            sistemaLegado::exibeAviso("Este documento já existe!","n_incluir","erro");
        } else {

            $obTLicitacaoDocumento->inclusao();

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),$_REQUEST['stNomeDocumento'],"incluir","aviso", Sessao::getId(), "../");
        }
    break;

    case 'excluir':
        $rsTLicitacaoLicitacaoDocumentos    = new RecordSet();
        $rsTLicitacaoCertificacaoDocumentos = new RecordSet();
        $boExcluir                          = true;

        $obTLicitacaoLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();
        $obTLicitacaoLicitacaoDocumentos->setDado("cod_documento",$_REQUEST['inCodDocumento']);
        $obTLicitacaoLicitacaoDocumentos->recuperaPorChave($rsTLicitacaoLicitacaoDocumentos);

        if ( $rsTLicitacaoLicitacaoDocumentos->getNumLinhas() > 0 ) {
            $boExcluir = false;
        }

        $obTLicitacaoCertificacaoDocumentos = new TLicitacaoCertificacaoDocumentos();
        $obTLicitacaoCertificacaoDocumentos->setDado("cod_documento",$_REQUEST['inCodDocumento']);
        $obTLicitacaoCertificacaoDocumentos->recuperaPorChave($rsTLicitacaoCertificacaoDocumentos);

        if ( $rsTLicitacaoCertificacaoDocumentos->getNumLinhas() > 0 ) {
            $boExcluir = false;
        }

        if ($boExcluir) {

           $obTLicitacaoDocumento->setDado("cod_documento",$_REQUEST['inCodDocumento']);
           $obTLicitacaoDocumento->exclusao();
           SistemaLegado::alertaAviso($pgList."?".Sessao::getId(),$_REQUEST['stNomDocumento'],"excluir","aviso", Sessao::getId(), "../");
        } else {
           $stMsg = "Erro ao excluir documento. Este documento está sendo utilizado pelo sistema.";
           SistemaLegado::alertaAviso($pgList."?".Sessao::getId(),$stMsg,"aviso","erro",Sessao::getId(),"../");
        }

    break;
}
Sessao::encerraExcecao();
?>
