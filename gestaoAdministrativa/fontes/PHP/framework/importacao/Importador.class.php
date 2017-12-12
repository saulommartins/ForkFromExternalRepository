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

set_time_limit(0);

/**
    * Classe responsável por trabalhar com os layouts de exportação de dados
    * @author Analista/Desenvolvedor: Diego Barbosa Victoria
    * @package Importador
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
class Importador
{
/**
    * @access Private
    * @var Array
*/
var $arArquivos;
/**
    * @access Private
    * @var Object
*/
var $roArquivo;

/**
    * Método Construtor
    * @access Private
*/
function Importador()
{
    $this->arArquivos = array();
}

function addArquivo($stNome)
{
    include_once ( CLA_ARQUIVO_IMPORTADOR );
    $this->arArquivos[]     = new ArquivoImportador( $this , $stNome );
    $this->roArquivo  = &$this->arArquivos[ count( $this->arArquivos ) -1 ];
}

function Show()
{
    for ($inCount=0; $inCount<count($this->arArquivos); $inCount++) {
        $obErro = $this->arArquivos[$inCount]->Ler();
        if (!$obErro->ocorreu()) {
            $arElementos = $this->arArquivos[$inCount]->rsRecordSet->getElementos();
            for ($inLinha = 0; $inLinha<count($arElementos);$inLinha++) {
                foreach ($arElementos[$inLinha] as $stColuna => $stValor) {
                    if ( strpos($stColuna,"chaveEstrangeira") !== false) {
                        $arColunaEstrangeira = explode ("_@!@_",$stColuna);
                        $stNomeArquivo  = $arColunaEstrangeira[1];
                        //Armazena a quantidade de arquivos, com campos e valores de FK
                        if ( !@in_array($arColunaEstrangeira[2], $arNomeColuna[$stNomeArquivo]) ) {
                            $arNomeColuna[$stNomeArquivo][]  = $arColunaEstrangeira[2];
                            $arValorColuna[$stNomeArquivo][] = $stValor;
                        }
                    }
                }
                if ( count($arNomeColuna) > 0 ) {
                    //$inCountErrosLinha = 0;

                    //echo "inLinha=$inLinha<br>";

                    //echo("<pre><hr>");print_R($arNomeColuna);print_r($arValorColuna);echo ("</pre><hr>");
                    foreach ($arNomeColuna as $stNomeArquivoCSV=>$stColunaArquivo) {
                        $inCountErrosLinha = 0;
                        //
                        //$arErrosLinha = array();
                        for ($inCountColuna=0; $inCountColuna<count($arNomeColuna); $inCountColuna++) {
                            //Inicia validação de chave
                            for ($inCountArquivos=0; $inCountArquivos<count($this->arArquivos); $inCountArquivos++) {
                                //echo $this->arArquivos[$inCountArquivos]->getNomeArquivo()." == ".$stNomeArquivoCSV;
                                //echo "  ".$this->arArquivos[$inCount]->getNomeArquivo()." != ".$this->arArquivos[$inCountArquivos]->getNomeArquivo();
                                //echo "<br>";
                                if(    $this->arArquivos[$inCountArquivos]->getNomeArquivo() == $stNomeArquivoCSV
                                    && $this->arArquivos[$inCount]->getNomeArquivo() != $this->arArquivos[$inCountArquivos]->getNomeArquivo() ){

                                    //print_r( $this->arArquivos[$inCountArquivos]->rsRecordSet );
                                    //echo "<br>";
                                    $stLinhaRSAnterior = "";
                                    $stLinhaAtual      = "";
                                    $this->arArquivos[$inCountArquivos]->rsRecordSet->setPrimeiroElemento();
//                                    echo "Arquivo=> ".$this->arArquivos[$inCount]->getNomeArquivo()."<br>";

                                    //echo "NumLinhas=>".$this->arArquivos[$inCountArquivos]->rsRecordSet->getNumLinhas()."<br>";

                                    $inNumLinhasArqFK = $this->arArquivos[$inCountArquivos]->rsRecordSet->getNumLinhas();
                                    while ( !$this->arArquivos[$inCountArquivos]->rsRecordSet->eof() ) {
                                        $stLinhaAtual = "";
                                        $stLinhaRSAnterior = "";
                                        for ($inCountCol=0; $inCountCol<count($arNomeColuna[$stNomeArquivoCSV]); $inCountCol++) {
                                            //echo $this->arArquivos[$inCountArquivos]->rsRecordSet->getCampo( $arNomeColuna[$stNomeArquivoCSV][$inCountCol] )."<br>";
                                            //echo $arNomeColuna[$stNomeArquivoCSV][$inCountCol]."<br>";
                                            $stLinhaRSAnterior .= $this->arArquivos[$inCountArquivos]->rsRecordSet->getCampo( $arNomeColuna[$stNomeArquivoCSV][$inCountCol] );
                                            //  echo("<pre><hr>");print_R( $arElementos[$inLinha] );echo ("</pre><hr>");
                                            //  echo "DN=".$arElementos[$inLinha][ "Descrição" ];
                                            //  echo "MA=".$arElementos[$inLinha][ "Máscara" ];
                                            //  echo "<br>";
                                            //echo("<pre><hr>");print_R( $arNomeColuna[$stNomeArquivoCSV] );echo ("</pre><hr>");
                                            //echo("<pre><hr>");print_R( $this->arArquivos[$inCountArquivos]->getNomeArquivo() );echo ("</pre><hr>");

                                            $stLinhaAtual = "";
                                            $stNomeColunasArquivo = "";
                                            $stMsgErroFKArq = $this->arArquivos[$inCountArquivos]->getNomeArquivo();
                                            for ($inCountColArq=0; $inCountColArq<count($this->arArquivos[$inCountArquivos]->arColunas); $inCountColArq++) {
                                                //echo $this->arArquivos[$inCountArquivos]->arColunas[$inCountColArq]->getCampo()."<br>";
                                                //echo("<pre><hr>");print_R($arElementos[$inLinha]);echo ("</pre><hr>");
                                                $stCampoTMP  = "chaveEstrangeira_@!@_".$this->arArquivos[$inCountArquivos]->getNomeArquivo();
                                                $stCampoTMP .= "_@!@_".$this->arArquivos[$inCountArquivos]->arColunas[$inCountColArq]->getCampo();
                                                //echo $stCampoTMP."<br>";
                                                $stLinhaAtual .= $arElementos[$inLinha][$stCampoTMP];
                                                //$stLinhaAtual .= $arElementos[$inLinha][ $arNomeColuna[$stNomeArquivoCSV][$inCountColArq] ];
                                                //$arNomeColuna[$stNomeArquivoCSV]
                                                $stNomeColunasArquivo .= " [".$this->arArquivos[$inCountArquivos]->arColunas[$inCountColArq]->getCampo()."] ";
                                            }
                                        }

//                                        echo "VALIDACAO=>  $stLinhaRSAnterior == $stLinhaAtual <br>";

                                        $stNomeArquivoAtual = $this->arArquivos[$inCount]->getNomeArquivo();
                                        if ($stLinhaRSAnterior == $stLinhaAtual) {
                                            //$arErro[] = ($inLinha+1);

                                            $stNomeArquivoReferenciado = $this->arArquivos[$inCountArquivos]->getNomeArquivo();
                                            //$arElementos = array();
                                            //break 4;
                                            break 3;
                                        } else {
                                            $inCountErrosLinha++;
                                            //
                                            //  if(!in_array($stLinhaAtual,$arErrosLinha))
                                            //      $arErrosLinha[] = $stLinhaAtual;
                                            //echo $inCountErrosLinha."<br>";
                                        }
                                        $this->arArquivos[$inCountArquivos]->rsRecordSet->proximo();
                                    }
                                    //echo $stLinhaRSAnterior."<br>";

                                    //for ($inCountArq=0; $inCountArq<count($this->arArquivos); $inCountArq++) {
                                    //    echo("<pre><hr>");print_R($stColunaArquivo);echo ("</pre><hr>");
                                    //    echo $this->arArquivos[$inCountArquivos]->getNomeArquivo()."<br>";
                                    //}
                                }

                                //$arNomeColuna[$stNomeArquivoCSV][$inCountColuna] ;
                                //$arValorColuna[$stNomeArquivoCSV][$inCountColuna];
                            }

                            //echo "$inCountErrosLinha ---- $inNumLinhasArqFK<br>";

                            if ($inCountErrosLinha == $inNumLinhasArqFK) {
                                //$arErroLinha[] = ($inLinha+1);
                                $inCountLinhasErro++;
                                //echo "inCountLinhasErro=>$inCountLinhasErro<br>";
                                //echo "AXXX="."$stNomeArquivoAtual $stMsgErroFKArq<br>";
                                break 3;
                            }
                        }
                    }
                    $arNomeColuna  = array();
                    $arValorColuna = array();

                    //if ($inCountErrosLinha == $inNumLinhasArqFK) {
                    //    //$arErroLinha[] = ($inLinha+1);
                    //    $inCountLinhasErro++;
                    //    echo "ERRO<BR>";
                    //}
                }
            }

            //echo("<pre><hr>");print_R($arErro);echo ("</pre><hr>");

        } else {
            break;
        }
        if ($inCountLinhasErro>0) {
            //$obErro->setDescricao("O Valor para os campos $stNomeColunasArquivo do Arquivo $stNomeArquivoAtual não está presente no Arquivo Referenciado $stNomeArquivoReferenciado ");
            //$obErro->setDescricao("Linha: ".implode($arErro,',')." do Arquivo $stNomeArquivoAtual não existe no Arquivo $stMsgErroFKArq");
              //$obErro->setDescricao("Existe(m) $inCountLinhasErro linha(s) do Arquivo $stNomeArquivoAtual não existe no Arquivo $stMsgErroFKArq, na(s) chave(s) de pesquisa(s) (".implode(",",$arErrosLinha).")");
              $obErro->setDescricao("A linha que possui o registro [$stLinhaAtual] do Arquivo $stNomeArquivoAtual não existe no Arquivo $stMsgErroFKArq");
            //echo $stMsgErroFKArq;
            //echo $stNomeArquivoAtual;
            break;
        }
    }

    return $obErro;
}
}
?>
