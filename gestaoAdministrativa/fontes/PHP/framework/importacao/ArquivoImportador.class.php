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

/**
    * Classe para trabalhar com arquivos texto no exportador
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Importador
*/
include_once( CLA_ARQUIVO_CSV );
class ArquivoImportador extends ArquivoCSV
{
/**
    * @access Private
    * @var String
*/
var $stDelimitadorColuna;
/**
    * @access Private
    * @var String
*/
var $stDelimitadorTexto;
/**
    * @access Private
    * @var Array
*/
var $arColunas;
/**
    * @access Private
    * @var Object
*/
var $roColuna;
/**
    * @access Private
    * @var Object
*/
var $roImportador;
/**
    * @access Private
    * @var Object
*/
var $rsRecordSet;

/**
    * @access Public
    * @param String $valor
*/
function setDelimitadorColuna($valor) { $this->stDelimitadorColuna= $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDelimitadorTexto($valor) { $this->stDelimitadorTexto= $valor; }

/**
    * @access Public
    * @Return String
*/
function getDelimitadorColuna() { return $this->stDelimitadorColuna; }

/**
    * @access Public
    * @Return String
*/
function getDelimitadorTexto() { return $this->stDelimitadorTexto; }

/**
    * Método Construtor
    * @access Private
*/
function ArquivoImportador(&$roImportador, $stNome)
{
    $this->roImportador   = &$roImportador;
    $this->stTipoDocumento= null;
    $this->arColunas       = array();
    $this->rsRecordSet     = new RecordSet;
    parent::ArquivoCSV( $stNome );
}

function addColuna($stCampo ,$stTipo)
{
    include_once( CLA_ARQUIVO_IMPORTADOR_COLUNA );
    $this->arColunas[]    = new ArquivoImportadorColuna( $this,$stCampo,$stTipo );
    $this->roColuna = &$this->arColunas[ count( $this->arColunas ) -1 ];
}

function Ler($stModo = 'r')
{
    $this->Abrir($stModo);
    $stChavePrimaria = null;
    $inCountLinha = 0;
    if ( !$this->obErro->ocorreu() ) {
        if ( $this->getTamanho()==0 ) {
            $this->obErro->setDescricao("O Arquivo ".$this->getNomeArquivo()." não pode ser vazio.");
        } else {
            while ($arLinha =  $this->LerLinha()) {
                if (count($arLinha) != count($this->arColunas)) {
                    //print_r($arLinha);
                    //echo "--".count($arLinha)." != ".count($this->arColunas)."<br>";
                    $this->obErro->setDescricao("Devem existir ".(count($this->arColunas))." colunas no arquivo ".$this->getNomeArquivo());
                }
                if (!$this->obErro->ocorreu()) {
                    for ($inCount=0;$inCount < count($this->arColunas);$inCount++ ) {
                        $this->arColunas[$inCount]->Validar($arLinha[$inCount]);
                        if (!$this->obErro->ocorreu()) {
                            $arRecordSet[($inCountLinha)][$this->arColunas[($inCount)]->getCampo()] = $arLinha[$inCount];
                            if ($this->arColunas[($inCount)]->getChavePrimaria() == true) {
                                $stChavePrimaria .= (!strstr($stChavePrimaria,$this->arColunas[($inCount)]->getCampo()) ) ? "[".$this->arColunas[($inCount)]->getCampo()."] " : "";
                                $arRecordSet[($inCountLinha)]["chavePrimaria"] .= $arLinha[$inCount];
                            }
                            $stColunas = "";
                            if ($this->arColunas[($inCount)]->getChaveEstrangeiraArquivo() != null) {
                                //for ($inCountColuna=0;$inCountColuna < count($this->arColunas);$inCountColuna++ ) {
                                //    if ($this->arColunas[$inCount]->getChaveEstrangeiraArquivo() == $this->arColunas[$inCountColuna]->getChaveEstrangeiraArquivo()) {
                                         $stColunas .= (!strstr($stColunas,$this->arColunas[$inCount]->getChaveEstrangeiraColuna())) ?  "_@!@_".$this->arColunas[$inCount]->getChaveEstrangeiraColuna()."" : "" ;
                                //    }
                                //}
                                $stChaveEstrangeira = "chaveEstrangeira_@!@_".$this->arColunas[$inCount]->getChaveEstrangeiraArquivo()."";
                                $stChaveEstrangeira .= $stColunas;
                                $arRecordSet[($inCountLinha)][$stChaveEstrangeira] .= $arLinha[$inCount];
                                //$arRecordSet[($inCountLinha)]["chaveEstrangeira".$this->arColunas[$inCount]->getChaveEstrangeiraArquivo()] .= array($this->arColunas[$inCount]->getChaveEstrangeiraColuna(),$arLinha[$inCount]);
                            }
                        } else {
                            //echo("<pre><hr>");print_R($arLinha);echo ("</pre><hr>");
                            break 2;
                        }
                    }
                }
                $inCountLinha++;
            }
            if (!$this->obErro->ocorreu()) {
                $this->rsRecordSet->preenche($arRecordSet);
                //echo("<pre><hr>");print_R($this->rsRecordSet);echo ("</pre><hr>");
                //Validação de Chave Primária
                if ($stChavePrimaria != null) {
                    $this->rsRecordSet->ordena("chavePrimaria",ASC,SORT_REGULAR);

                    while (!$this->rsRecordSet->eof()) {
                         $stCorrente = $this->rsRecordSet->getCampo("chavePrimaria");
                         if ($stCorrente == $stAnterior) {
                            $this->obErro->setDescricao("Existem registros duplicados no arquivo ".$this->getNomeArquivo()." para a chave  ".$stChavePrimaria."");
                            break;
                         }
                         $stAnterior = $this->rsRecordSet->getCampo("chavePrimaria");
                         $this->rsRecordSet->proximo();
                    }
                    $this->rsRecordSet->setPrimeiroElemento();
                }
            }
        }
    }

/*

        for ($inCBlocos=0; $inCBlocos<count($this->arBlocos); $inCBlocos++) {
            $this->arBlocos[$inCBlocos]->Formatar();
            if ( !$this->obErro->ocorreu() ) {
                for ($inCLinhas=0; $inCLinhas<count($this->arBlocos[$inCBlocos]->arLinhas); $inCLinhas++) {
                    $stLinha = $this->arBlocos[$inCBlocos]->arLinhas[$inCLinhas];
                    $this->addLinha($stLinha);
                }
            }
        }

    if ( !$this->obErro->ocorreu() ) {
        parent::Ler( $stModo );
    }
*/

    return $this->obErro;
}

function FormataTipoDocumento()
{
}

}
?>
