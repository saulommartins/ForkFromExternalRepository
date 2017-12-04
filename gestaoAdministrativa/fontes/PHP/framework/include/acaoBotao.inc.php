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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

$arAcao = array();
$arAcao["DESARQUIVAR"]     = IMG_DESARQUIVAR;
$arAcao["DESPACHAR"]       = IMG_DESPACHAR;
$arAcao["ERRO"]            = IMG_ERRO;
$arAcao["POPUP"]           = IMG_POPUP;
$arAcao["RECEBER"]         = IMG_RECEBER;
$arAcao["ALTERAR"]         = IMG_EDITAR;
$arAcao["ALTERAR16PX"]     = IMG_EDITAR_16PX;
$arAcao["PRORROGAR"]       = IMG_EDITAR;
$arAcao["EXCLUIR"]         = IMG_EXCLUIR;
$arAcao["REMOVER"]         = IMG_REMOVER;
$arAcao["EXCLUIR16PX"]     = IMG_EXCLUIR_16PX;
$arAcao["INCLUIR"]         = IMG_INCLUIR;
$arAcao["BAIXAR"]          = IMG_BAIXAR;
$arAcao["BAIXAR15PX"]      = IMG_BAIXAR_15PX;
$arAcao["SUBIR"]           = IMG_SUBIR;
$arAcao["SUBIR15PX"]       = IMG_SUBIR_15PX;
$arAcao["CONSULTAR"]       = IMG_CONSULTAR;
$arAcao["SELECIONAR"]      = IMG_SELECIONAR;
$arAcao["EFETIVAR"]        = IMG_SELECIONAR;
$arAcao["RENOMEAR"]        = IMG_RENOMEAR;
$arAcao["HISTORICO"]       = IMG_EDITAR;
$arAcao["SUSPENDER"]       = IMG_BAIXAR;
$arAcao["CASSAR"]          = IMG_BAIXAR;
$arAcao["CANCELAR"]        = IMG_BAIXAR;
$arAcao["ATIVIDADE"]       = IMG_EDITAR;
$arAcao["ESPECIAL"]        = IMG_EDITAR;
$arAcao["DETALHAR"]        = IMG_DETALHAR;
$arAcao["REFORMA"]         = IMG_REFORMA;
$arAcao["AGLUTINAR"]       = IMG_AGLUTINAR;
$arAcao["DESMEMBRAR"]      = IMG_DESMEMBRAR;
$arAcao["VALIDAR"]         = IMG_SELECIONAR;
$arAcao["ELEMENTO"]        = IMG_EDITAR;
$arAcao["NATUREZA"]        = IMG_EDITAR;
$arAcao["DOMICILIO"]       = IMG_EDITAR;
$arAcao["HORARIO"]         = IMG_EDITAR;
$arAcao["SOCIEDADE"]       = IMG_EDITAR;
$arAcao["DEF"]             = IMG_EDITAR;
$arAcao["VISUALIZAR"]      = IMG_CONSULTAR;
$arAcao["IMOVEL"]          = IMG_IMOVEL;
$arAcao["LOTE"]            = IMG_LOTE;
$arAcao["PROPRIETARIO"]    = IMG_PROPRIETARIO;
$arAcao["RELATORIO"]       = IMG_RELATORIO;
$arAcao["TRANSF"]          = IMG_TRANSFERENCIA;
$arAcao["CONDOMINIO"]      = IMG_CONDOMINIO;
$arAcao["ALIQUOTA"]        = IMG_ALIQUOTA;
$arAcao["EMPRESA"]         = IMG_EMPRESA;
$arAcao["RESPONSAVEL"]     = IMG_PROPRIETARIO;
$arAcao["ATIVIDADE"]       = IMG_ATIVIDADE;
$arAcao["LICENCA"]         = IMG_LICENCA;
$arAcao["RESCINDIR"]       = IMG_RESCINDIR;
$arAcao["RESUMIR"]         = IMG_RESUMIR;
$arAcao["DEFINIR"]         = IMG_EDITAR;
$arAcao["DEOFICIO"]        = IMG_BAIXAR;
$arAcao["FORMULA"]         = IMG_FORMULA;
$arAcao["ATUALIZAR"]       = IMG_ATUALIZAR;
$arAcao["ABRIR"]           = IMG_ABRIR;
$arAcao["REABRIR"]         = IMG_ABRIR;
$arAcao["ANULAR"]          = IMG_ANULAR;
$arAcao["CONVERTER"]       = IMG_CONVERTER;
$arAcao["SALVAR"]          = IMG_SALVAR;
$arAcao["LIMPAR"]          = IMG_LIMPAR;
$arAcao["ESTORNAR"]        = IMG_ESTORNAR;
$arAcao["AVANCARPROC"]     = IMG_AVANCAR_PROC;
$arAcao["DRAGDROP"]        = IMG_DRAGDROP;
$arAcao["PDF"]             = IMG_PDF;
$arAcao["USUARIO"]         = IMG_USUARIO;
$arAcao["ATIVAR"]          = IMG_ATIVOINATIVO;
$arAcao["DESATIVAR"]       = IMG_ATIVOINATIVO;
$arAcao["ATIVOINATIVO"]    = IMG_ATIVOINATIVO;
$arAcao["IMPRIMIR"]        = IMG_IMPRIMIR;
$arAcao["PUBLICAR"]        = IMG_PUBLICAR;
$arAcao["CLASSIFICAR"]     = IMG_CLASSIFICAR;
$arAcao["DESCLASSIFICAR"]  = IMG_DESCLASSIFICAR;
$arAcao["CLASSIFICACAO"]   = IMG_ATIVOINATIVO;
$arAcao["DOCUMENTOS"]      = IMG_RELATORIO;
$arAcao["CONCEDER"]        = IMG_CONCEDER;
$arAcao["PROCESSAR"]       = IMG_PROCESSAR;

//ARRAY TITLES
$arTitle["HISTORICO"]      = "Alterar Características";
$arTitle["DEF"]            = "Definir";
$arTitle["NATUREZA"]       = "Alterar";
$arTitle["DOMICILIO"]      = "Alterar";
$arTitle["HORARIO"]        = "Alterar";
$arTitle["SOCIEDADE"]      = "Alterar";
$arTitle["ELEMENTO"]       = "Alterar";
$arTitle["IMOVEL"]         = "Imóvel";
$arTitle["PROPRIETARIO"]   = "Proprietário";
$arTitle["RELATORIO"]      = "Relatório";
$arTitle["TRANSF"]         = "Transferência";
$arTitle["CONDOMINIO"]     = "Condomínio";
$arTitle["RESUMIR"]        = "Cancelar Detalhamento";
$arTitle["RESCINDIR"]      = "Rescindir Contrato";
$arTitle["LICENCA"]        = "Licença";
$arTitle["RESPONSAVEL"]    = "Responsável";
$arTitle["FORMULA"]        = "Fórmula";
$arTitle["AVANCARPROC"]    = "Avançar";
$arTitle["USUARIO"]        = "Incluir";
$arTitle["ATIVOINATIVO"]   = "Ativar/Desativar";
$arTitle["CLASSIFICACAO"]  = "Classificar/Desclassificar";
$arTitle["CONCEDER"]       = "Conceder";
