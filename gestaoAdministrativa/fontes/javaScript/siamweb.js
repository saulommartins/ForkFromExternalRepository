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
/**
* Classe Urbem
* Data de Criação: 11/04/2007


* @author Desenvolvedor: Lucas Stephanou

$Revision: 21929 $
$Name$
$Author: domluc $
$Date: 2007-04-17 16:25:41 -0300 (Ter, 17 Abr 2007) $

Casos de uso: uc-01.01.00
*/

/* inicialição basica */

var Urbem = {
    Author: "Lucas Stephanou",
    theme_path: "../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/"   
};

Urbem.Grid = {
    IMG_DESARQUIVAR   : Urbem.theme_path + "imagens/botao_desarquiva.png"  , 
    IMG_DESPACHAR     : Urbem.theme_path + "imagens/botao_despachar.png"   , 
    IMG_ERRO          : Urbem.theme_path + "imagens/botao_erro.png"        , 
    IMG_POPUP         : Urbem.theme_path + "imagens/botao_popup.png"       , 
    IMG_RECEBER       : Urbem.theme_path + "imagens/botao_receber.png"     , 
    IMG_EDITAR        : Urbem.theme_path + "imagens/btneditar.gif"         , 
    IMG_EDITAR_16PX   : Urbem.theme_path + "imagens/btneditar16px.png"     , 
    IMG_EXCLUIR       : Urbem.theme_path + "imagens/btnexcluir.gif"        , 
    IMG_REMOVER       : Urbem.theme_path + "imagens/btnexcluir.gif"        , 
    IMG_EXCLUIR_16PX  : Urbem.theme_path + "imagens/btnexcluir16px.png"    , 
    IMG_INCLUIR       : Urbem.theme_path + "imagens/btnincluir.gif"        , 
    IMG_BAIXAR        : Urbem.theme_path + "imagens/botao_expandir.png"    , 
    IMG_BAIXAR_15PX   : Urbem.theme_path + "imagens/botao_expandir15px.png", 
    IMG_SUBIR         : Urbem.theme_path + "imagens/botao_retrair.png"     , 
    IMG_SUBIR_15PX    : Urbem.theme_path + "imagens/botao_retrair15px.png" , 
    IMG_CONSULTAR     : Urbem.theme_path + "imagens/look.gif"              , 
    IMG_RENOMEAR      : Urbem.theme_path + "imagens/btnrenomear.png"       , 
    IMG_SELECIONAR    : Urbem.theme_path + "imagens/btnselecionar.png"     , 
    IMG_CASSAR        : Urbem.theme_path + "imagens/btncassar.png"         , 
    IMG_SUSPENDER     : Urbem.theme_path + "imagens/btnsuspender.png"      , 
    IMG_CANCELAR      : Urbem.theme_path + "imagens/btncancelar.png"       , 
    IMG_DETALHAR      : Urbem.theme_path + "imagens/btndetalhar.png"       , 
    IMG_REFORMA       : Urbem.theme_path + "imagens/btnreforma.png"        , 
    IMG_AGLUTINAR     : Urbem.theme_path + "imagens/btnaglutinar.png"      , 
    IMG_DESMEMBRAR    : Urbem.theme_path + "imagens/btndesmembrar.png"     , 
    IMG_IMOVEL        : Urbem.theme_path + "imagens/btnImovel.png"         , 
    IMG_LOTE          : Urbem.theme_path + "imagens/btnLote.png"           , 
    IMG_PROPRIETARIO  : Urbem.theme_path + "imagens/btnProprietario.png"   , 
    IMG_RELATORIO     : Urbem.theme_path + "imagens/btnRelatorio.png"      , 
    IMG_TRANSFERENCIA : Urbem.theme_path + "imagens/btnTransferencia.png"  , 
    IMG_CONDOMINIO    : Urbem.theme_path + "imagens/btnCondominio.png"     , 
    IMG_ALIQUOTA      : Urbem.theme_path + "imagens/btnAliquota.png"       , 
    IMG_EMPRESA       : Urbem.theme_path + "imagens/btnEmpresa.png"        , 
    IMG_ATIVIDADE     : Urbem.theme_path + "imagens/btnAtividade.png"      , 
    IMG_LICENCA       : Urbem.theme_path + "imagens/btnLicenca.png"        , 
    IMG_RESCINDIR     : Urbem.theme_path + "imagens/btnRescindir.png"      , 
    IMG_RESUMIR       : Urbem.theme_path + "imagens/btnResumir.png"        , 
    IMG_FORMULA       : Urbem.theme_path + "imagens/btnFormula.png"        , 
    IMG_ATUALIZAR     : Urbem.theme_path + "imagens/btnRefresh.png"        , 
    IMG_ABRIR         : Urbem.theme_path + "imagens/btnAbrir.png"          , 
    IMG_ANULAR        : Urbem.theme_path + "imagens/btnAnular.png"         , 
    IMG_CONVERTER     : Urbem.theme_path + "imagens/btnConverter.png"      , 
    IMG_SALVAR        : Urbem.theme_path + "imagens/botao_salvar.png"      , 
    IMG_LIMPAR        : Urbem.theme_path + "imagens/btnLimparr.png"        , 
    IMG_ESTORNAR      : Urbem.theme_path + "imagens/btnEstornar.png"       , 
    IMG_AVANCAR_PROC  : Urbem.theme_path + "imagens/btnAvancaProcesso.png" , 
    IMG_DRAGDROP      : Urbem.theme_path + "imagens/btnDragDrop.png"       , 
    IMG_PDF           : Urbem.theme_path + "imagens/btnPDF.png"            , 
    IMG_USUARIO       : Urbem.theme_path + "imagens/btnUsuario.png"        , 
    IMG_ATIVOINATIVO  : Urbem.theme_path + "imagens/btnAtivoInativo.png"   , 
    IMG_IMPRIMIR      : Urbem.theme_path + "imagens/botao_imprimir.png"    
}
