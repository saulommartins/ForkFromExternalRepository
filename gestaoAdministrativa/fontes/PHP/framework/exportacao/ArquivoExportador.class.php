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

  $Id: ArquivoExportador.class.php 66083 2016-07-18 17:37:53Z lisiane $

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Classe para trabalhar com arquivos texto no exportador
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Exportador
*/
include_once( CLA_ARQUIVO_TEXTO );
class ArquivoExportador extends ArquivoTexto
{
/**
    * @access Private
    * @var String
*/
var $stTipoDocumento;
/**
    * @access Private
    * @var Array
*/
var $arBlocos;
/**
    * @access Private
    * @var Object
*/
var $roUltimoBloco;
/**
    * @access Private
    * @var Object
*/
var $roExportador;
/**
    * @access Private
    * @var Object
*/
var $obTConfiguracao;

var $stNomeArquivo;

public function setTitulo ( $valor = "" ) { $this->stTitulo = $valor; }
public function getTitulo ( ) { return $this->stTitulo; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoDocumento($valor) { $this->stTipoDocumento= $valor; }

/**
    * @access Public
    * @Return String
*/
function getTipoDocumento() { return $this->stTipoDocumento; }

/**
    * Método Construtor
    * @access Private
*/
function ArquivoExportador(&$roExportador, $stNome)
{
    $this->roExportador   = &$roExportador;
    $this->obTConfiguracao = new TAdministracaoConfiguracao();
    $this->stTipoDocumento= null;
    $this->arBlocos       = array();
    parent::ArquivoTexto( $stNome );
}

function addBloco(&$rsRecordSet)
{
    include_once( CLA_ARQUIVO_EXPORTADOR_BLOCO );
    $this->arBlocos[]    = new ArquivoExportadorBloco( $this, $rsRecordSet );
    $this->roUltimoBloco = $this->arBlocos[ count( $this->arBlocos ) -1 ];
}

function Gravar($stModo = 'w+')
{
    $this->FormataTipoDocumento();

    if ( !$this->obErro->ocorreu() ) {
        for ($inCBlocos=0; $inCBlocos<count($this->arBlocos); $inCBlocos++) {
            $this->arBlocos[$inCBlocos]->Formatar();
            if ( !$this->obErro->ocorreu() ) {
                $count=count($this->arBlocos[$inCBlocos]->arLinhas);
                for ($inCLinhas=0; $inCLinhas<$count; $inCLinhas++) {
                    $stLinha = $this->arBlocos[$inCBlocos]->arLinhas[$inCLinhas];
                    unset($this->arBlocos[$inCBlocos]->arLinhas[$inCLinhas]);
                    $this->addLinha($stLinha);
                }
            }
        }
    }
    if ( !$this->obErro->ocorreu() ) {
        parent::Gravar( $stModo );
    }

    return $this->obErro;
}

function FormataTipoDocumento()
{
    switch (trim($this->stTipoDocumento)) {
        case "TCE_RS":

            if ( count($this->arBlocos) != 1) {
                $this->obErro->setDescricao("No TCE_RS pode existir somente um Bloco de dados.");
            } else {
                $arBlocos       = $this->arBlocos;
                $this->arBlocos = array();
                $arFiltro       = Sessao::read('exp_arFiltro');

                $arCNPJSetor = explode('|', $arFiltro['stCnpjSetor']);
                $stCnpj = $arCNPJSetor[1];
                $stNomPrefeitura = $arCNPJSetor[2];

                $arCabecalho[0]['cnpj']         = $stCnpj;
                $arCabecalho[0]['dt_inicial']   = $arFiltro['stDataInicio'];
                $arCabecalho[0]['dt_final']     = $arFiltro['stDataFinal'];
                $arCabecalho[0]['dt_geracao']   = date('d/m/Y',time());
                $arCabecalho[0]['nom_setor']    = $stNomPrefeitura;
                $rsCabecalho = new RecordSet;
                $rsCabecalho->preenche($arCabecalho);

                $this->addBloco($rsCabecalho);
                $this->roUltimoBloco->addColuna("cnpj");
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                $this->roUltimoBloco->addColuna("dt_inicial");
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                $this->roUltimoBloco->addColuna("dt_final");
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                $this->roUltimoBloco->addColuna("dt_geracao");
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);
                $this->roUltimoBloco->addColuna("nom_setor");
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

                    /*if ($_REQUEST['stAcao'] == 'disposicao') {
                        $this->roUltimoBloco->addColuna("nom_setor");
                        $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);
                    }*/

                for ($i = 0;$i < count($arBlocos); $i++) {
                    $this->arBlocos[$i+1] = $arBlocos[$i];
                }

                //Cópia do corpo
                $inCountBlocos = count($this->arBlocos)-1;
                if ( $this->arBlocos[ $inCountBlocos ]->getDelimitador() != '' ) {
                    $this->obErro->setDescricao("No TCE_RS não deve existir delimitador");
                } else {

                    //Conta quantidade de registros para inserir no rodapé
                    $arRodape[0]["quantidade_registros"] = $this->arBlocos[ $inCountBlocos ]->rsRecordSet->getNumLinhas();
                    if ($arRodape[0]["quantidade_registros"]==-1) { $arRodape[0]["quantidade_registros"]=0; };
                    $rsRodape = new RecordSet;
                    $rsRodape->preenche($arRodape);

                    //Rodapé
                    $this->addBloco($rsRodape);
                    $this->roUltimoBloco->addColuna("FINALIZADOR[]");
                    $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
                    $this->roUltimoBloco->addColuna("quantidade_registros");
                    $this->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
                }
            }
        break;

        case "TCM_BA":

            //Adiciona o cabecalho
            $arBlocos       = $this->arBlocos;
            $this->arBlocos = array();

            $arCabecalho = array();
            $arCabecalho[0]['dt_geracao']   = date('d/m/Y',time());
            $arCabecalho[0]['hr_geracao']   = date('H:i:s',time());
            $arCabecalho[0]['versao_layout']= '1';
            $arCabecalho[0]['sistema']      = 'SIGA';
            $arCabecalho[0]['cod_unidade']  = Sessao::read('cod_unidade_gestora');//$rsEntidade->getCampo('cod_unidade_gestora');
            $arCabecalho[0]['nom_unidade']  = Sessao::read('nom_unidade');//$rsEntidade->getCampo('nom_entidade');
            $arCabecalho[0]['titulo']       = $this->getTitulo();

            $rsCabecalho = new RecordSet();
            $rsCabecalho->preenche($arCabecalho);

            $this->addBloco($rsCabecalho);
            $this->roUltimoBloco->addColuna("[]0");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $this->roUltimoBloco->addColuna( "[]".$arArquivo[0] );

            if ($arCabecalho[0]['titulo'] != '') {
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(0);
                $this->roUltimoBloco->addColuna("titulo");
            }

            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            $this->roUltimoBloco->addColuna("dt_geracao");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            $this->roUltimoBloco->addColuna("hr_geracao");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            $this->roUltimoBloco->addColuna("versao_layout");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            $this->roUltimoBloco->addColuna("sistema");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            $this->roUltimoBloco->addColuna("cod_unidade");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            $this->roUltimoBloco->addColuna("nom_unidade");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
            $this->roUltimoBloco->addColuna("[]1");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

            for ($i = 0;$i < count($arBlocos); $i++) {
                $this->arBlocos[$i+1] = $arBlocos[$i];
            }

            //Adiciona o sequencial
            $inCountBlocos = count($this->arBlocos)-1;
            $arElementos = $this->arBlocos[ $inCountBlocos ]->rsRecordSet->arElementos;
            for ($inCount=1; $inCount<=count($arElementos); $inCount++) {
                $arElementos[ ($inCount-1) ]['sequencial_registros'] = ( $inCount + 1 );
            }
            $this->arBlocos[ $inCountBlocos ]->rsRecordSet->arElementos = $arElementos;

            //for ($inCount=0; $inCount<$inCountBlocos; $inCount++) {
                $this->arBlocos[ $inCountBlocos ]->addColuna("sequencial_registros");
                $this->arBlocos[ $inCountBlocos ]->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $this->arBlocos[ $inCountBlocos ]->roUltimaColuna->setTamanhoFixo(10);
            //}

            //Conta quantidade de registros para inserir no rodapé
            $arRodape[0]["quantidade_registros"] = $this->arBlocos[ ($inCountBlocos) ]->rsRecordSet->getNumLinhas();
            if ($arRodape[0]["quantidade_registros"]==-1) {
                $arRodape[0]["quantidade_registros"]=0;
            } else {
                $arRodape[0]["quantidade_registros"]=$arRodape[0]["quantidade_registros"]+2;
            }
            $rsRodape = new RecordSet;
            $rsRodape->preenche($arRodape);

            //Rodapé
            $this->addBloco($rsRodape);
            $this->roUltimoBloco->addColuna("[]9");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $this->roUltimoBloco->addColuna("quantidade_registros");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
        break;

        case "TCE_RN":
            $arquivos         = array('ANEXO21', 'ANEXO23', 'ANEXO26');
            ### Os arquivos em que não deve aparecer o sequencial
            $excluiSequencial = array('ANEXO26', 'ANEXO28', 'ANEXO23', 'ANEXO01DESPESA','ANEXO02DESPESAFUNCAO', 'ANEXO01RECEITA', 'ANEXO42', 'ANEXO27FUNDEFBBAAS');

            $arArquivo = explode('_',$this->getNomeArquivo());
            $arArquivo = explode('.',$arArquivo[1]);

            $inCountBlocos = count($this->arBlocos)-1;

            ###
            if (!in_array($arArquivo[0], $arquivos)) {
                $inSequencial = 1;
            } else {
                $inSequencial = 0;
            }

            for ($inTmp=0; $inTmp<=$inCountBlocos; $inTmp++) {

                $arElementos = $this->arBlocos[ $inTmp ]->rsRecordSet->arElementos;
                for ($inCount=1; $inCount<=count($arElementos); $inCount++) {
                    $arElementos[ ($inCount-1) ]['sequencial_registros'] = str_pad(++$inSequencial, 10, "0", STR_PAD_LEFT);
                }
                $this->arBlocos[ $inTmp ]->rsRecordSet->arElementos = $arElementos;

                if (!in_array($arArquivo[0], $excluiSequencial)) {
                    $this->arBlocos[ $inTmp ]->addColuna("sequencial_registros");
                    $this->arBlocos[ $inTmp ]->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                    $this->arBlocos[ $inTmp ]->roUltimaColuna->setTamanhoFixo(10);
                }
            }
            $inTamanhoColuna = 0;
            $inCountColunas = count($this->arBlocos[ $inCountBlocos ]->arColunas);
            for ($inCount=0; $inCount<$inCountColunas; $inCount++) {
                $inTamanhoColuna = $inTamanhoColuna + $this->arBlocos[ $inCountBlocos ]->arColunas[$inCount]->inTamanhoFixo;
            }

            ###
            if (!in_array($arArquivo[0], $arquivos)) {
                $arRodape[0]["quantidade_registros"] = $inSequencial + 1;
            } else {
                $arRodape[0]["quantidade_registros"] = str_pad(++$inSequencial, 10, "0", STR_PAD_LEFT);
            }

            $arCabecalho = array();

            switch ($arArquivo[0]) {
                case 'ANEXO01DESPESA':
                    $arCabecalho[0]['nome_arquivo'] = 'DESPESA' ;
                    break;
                case 'ANEXO01RECEITA':
                    $arCabecalho[0]['nome_arquivo'] = 'RECEITA';
                    break;
                case 'ANEXO02DESPESAFUNCAO':
                    $arCabecalho[0]['nome_arquivo'] = 'DESPFUNC';
                    break;
                case (substr($arArquivo[0], 0, 3) == 'LIC'):
                    $arCabecalho[0]['nome_arquivo'] = 'LICITACAO';
                    $inTamanhoColuna = 250;
                    break;
                case 'RECBBAAS':
                    $arCabecalho[0]['nome_arquivo'] = 'RECEITA';
                    break;
                case (substr($arArquivo[0], 0, 3) == 'EMP'):
                    $arCabecalho[0]['nome_arquivo'] = 'EMPENHO';
                    $inTamanhoColuna = 280;
                    break;
                case 'ANEXO42':
                    $arCabecalho[0]['nome_arquivo'] = 'ANEXO42';
                    $inTamanhoColuna = 151;
                    break;
                case "ANEXO27FUNDEFBBAAS":
                    $inTamanhoColuna = 160;
                    break;
                case "ANEXO28":
                    $arCabecalho[0]['nome_arquivo'] = 'ANEXO28';
                    $inTamanhoColuna = 160;
                    break;
            }

            ###
            if (!in_array($arArquivo[0], $arquivos)) {
                $arCabecalho[0]['bimestre_mes']         = Sessao::read('exp_bimestre');
                $arCabecalho[0]['bimestre_exercicio']   = Sessao::getExercicio();
                $arCabecalho[0]['dt_geracao']           = date('d/m/Y',time());
                $arCabecalho[0]['hr_geracao']           = date('H:i:s',time());
                $arCabecalho[0]['cod_orgao']            = Sessao::read('exp_stCodOrgao');
                $arCabecalho[0]['nom_orgao']            = Sessao::read('exp_stNomeEntidade');
                $rsCabecalho = new RecordSet;
                $rsCabecalho->preenche($arCabecalho);
                include_once( CLA_ARQUIVO_EXPORTADOR_BLOCO );
                $this->arBlocos[ 0 ]  = new ArquivoExportadorBloco( $this, $rsCabecalho );
                $this->arBlocos[ 0 ]->rsRecordSet->arElementos = $arCabecalho;
                $this->arBlocos[ 0 ]->addColuna("[]0");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(1);
                $this->arBlocos[ 0 ]->addColuna( "nome_arquivo" );
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(10);
                $this->arBlocos[ 0 ]->addColuna( "bimestre_exercicio" );
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(4);
                $this->arBlocos[ 0 ]->addColuna( "bimestre_mes" );
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(2);
                $this->arBlocos[ 0 ]->addColuna("[]O");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(1);
                $this->arBlocos[ 0 ]->addColuna("dt_geracao");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(10);
                $this->arBlocos[ 0 ]->addColuna("hr_geracao");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(8);
                $this->arBlocos[ 0 ]->addColuna("cod_orgao");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(4);
                $this->arBlocos[ 0 ]->addColuna("nom_orgao");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(100);

                if (!in_array($arArquivo[0], $excluiSequencial) || $arArquivo[0] == 'ANEXO28' || $arArquivo[0] == 'ANEXO42' || $arArquivo[0] == 'ANEXO27FUNDEFBBAAS') {
                    $this->arBlocos[ 0 ]->addColuna("[]");
                    $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                    $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo($inTamanhoColuna-150);
                    $this->arBlocos[ 0 ]->addColuna("[]1");
                    $this->arBlocos[ 0 ]->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                    $this->arBlocos[ 0 ]->roUltimaColuna->setTamanhoFixo(10);
                }
            }

            //Rodapé
            $rsRodape = new RecordSet;
            $rsRodape->preenche($arRodape);
            $this->addBloco($rsRodape);
            $this->roUltimoBloco->addColuna("[]9");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            $this->roUltimoBloco->addColuna("[]");
            $this->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo($inTamanhoColuna-11);

            if (!in_array($arArquivo[0], $excluiSequencial) || $arArquivo[0] == 'ANEXO28' || $arArquivo[0] == 'ANEXO27FUNDEFBBAAS') {
                $this->roUltimoBloco->addColuna("quantidade_registros");
                $this->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                $this->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
            }
        break;
        case 'TCM_GO':
            // Array arquivo sequencial automático
            $arArquivoSequencial = array( "HML", "ABL", "HBL", "JGL", "AEX");
            $arArquivo = explode('_',$this->getNomeArquivo());
            $arArquivo = explode('.',$arArquivo[1]);

            if( in_array(substr($arArquivo[0],0,-4), $arArquivoSequencial) ) {
                //Adiciona o sequencial
                $inCountBlocos = count($this->arBlocos)-1;
                $inSequencial = 0;
    
                for ($inTmp=0; $inTmp<=$inCountBlocos; $inTmp++) {
                    $arElementos = $this->arBlocos[ $inTmp ]->rsRecordSet->arElementos;
                    for ($inCount=1; $inCount<=count($arElementos); $inCount++) {
                        $arElementos[ ($inCount-1) ]['sequencial_registros'] = str_pad(++$inSequencial, 6, "0", STR_PAD_LEFT);
                    }
                    $this->arBlocos[ $inTmp ]->rsRecordSet->arElementos = $arElementos;
                    $this->arBlocos[ $inTmp ]->addColuna("sequencial_registros");
                    $this->arBlocos[ $inTmp ]->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                    $this->arBlocos[ $inTmp ]->roUltimaColuna->setTamanhoFixo(6);
                }
            }        
            break;
    }
}

}
?>
