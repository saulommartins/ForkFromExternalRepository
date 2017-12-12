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
    * Data de Criação   : 24/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore
    
    * @package framework
    * @subpackage componentes

    $Id: ListaMPDF.class.php 65308 2016-05-11 20:00:27Z jean $
*/


class ListaMPDF
{
    /**
        * @var Array
        * @access Private
    */
    private $arRecordSet          = Array();    
    private $inIndiceLista;
    private $arAssinatura         = Array();
    private $arFiltro             = Array();
    private $arCabecalhoGeral     = Array();
    private $stHtmlCabecalhoGeral;
    private $arCabecalho          = Array();
    private $arRodape             = Array();
    private $arCampo              = Array();
    private $arPagina             = Array();
    private $arQuebraLinha        = Array();
    private $arQuebraPagina       = Array();
    private $arClasse             = Array();
    private $stHtmlLista;
    
    private $obMPDF;
    private $stNomeRelatorio;
    private $inCodGestao;
    private $inCodModulo;
    private $stFormatoFolha = 'A4';
    private $stTipoSaida    = 'I';
    private $stCodEntidades;
    private $stDataInicio;
    private $stDataFinal;

    public function setRecordSet($valor)                    { $this->arRecordSet[]                   = $valor; }
    public function setAssinatura($valor)                   { $this->arAssinatura                    = $valor; }
    public function setFiltro($valor)                       { $this->arFiltro[]                      = $valor; }
    public function setIndiceLista($valor)                  { $this->inIndiceLista                   = $valor; }
    public function setCabecalhoGeral($valor)               { $this->arCabecalhoGeral[]              = $valor; }
    public function setHtmlCabecalhoGeral($valor)           { $this->stHtmlCabecalhoGeral            = $valor; }
    public function setCabecalho($indice, $valor)           { $this->arCabecalho[$indice][]          = $valor; }
    public function setRodape($indice, $valor)              { $this->arRodape[$indice][]             = $valor; }
    public function setCampo($indice, $valor)               { $this->arCampo[$indice][]              = $valor; }
    public function setPagina($indice, $valor)              { $this->arPagina[$indice]               = $valor; }
    public function setQuebraLinha($indice, $valor)         { $this->arQuebraLinha[$indice]          = $valor; }
    public function setQuebraPagina($indice, $valor)        { $this->arQuebraPagina[$indice]         = $valor; }
    public function setClasse($indice, $valor)              { $this->arClasse[$indice]               = $valor; }
    public function setHtmlLista($valor)                    { $this->stHtmlLista                     = $valor; }

    public function getRecordSet()                  { return $this->arRecordSet;                    }
    public function getRecordSetIndice($indice)     { return $this->arRecordSet[$indice];           }
    public function getIndiceLista()                { return $this->inIndiceLista;                  }
    public function getAssinatura()                 { return $this->arAssinatura;                   }
    public function getFiltro()                     { return $this->arFiltro;                       }
    public function getCabecalhoGeral()             { return $this->arCabecalhoGeral;               }
    public function getHtmlCabecalhoGeral()         { return $this->stHtmlCabecalhoGeral;           }
    public function getCabecalho($indice)           { return $this->arCabecalho[$indice];           }
    public function getRodape($indice)              { return $this->arRodape[$indice];              }
    public function getCampo($indice)               { return $this->arCampo[$indice];               }
    public function getPaginas()                    { return $this->arPagina;                       }
    public function getPaginaIndice($indice)        { return $this->arPagina[$indice];              }
    public function getQuebraLinha($indice)         { return $this->arQuebraLinha[$indice];         }
    public function getQuebraPagina($indice)        { return $this->arQuebraPagina[$indice];        }
    public function getClasse($indice)              { return $this->arClasse[$indice];              }
    public function getHtmlLista()                  { return $this->stHtmlLista;                    }

    //Classe MPDF sem estender
    public function setObMPDF($valor)          { $this->obMPDF              = $valor; }
    public function setNomeRelatorio($valor)   { $this->stNomeRelatorio     = $valor; }
    public function setCodGestao( $valor )     { $this->inCodGestao         = $valor; }
    public function setCodModulo( $valor )     { $this->inCodModulo         = $valor; }
    /* recebera A4 para retrato ou A4-L para paisagem */
    public function setFormatoFolha($valor)    { $this->stFormatoFolha      = $valor; }
    public function setTipoSaida($valor)       { $this->stTipoSaida         = $valor; }
    public function setCodEntidades( $valor )  { $this->stCodEntidades      = $valor; }
    public function setDataInicio( $valor )    { $this->stDataInicio        = $valor; }
    public function setDataFinal( $valor )     { $this->stDataFinal         = $valor; }

    public function getObMPDF()        { return $this->obMPDF;             }
    public function getNomeRelatorio() { return $this->stNomeRelatorio;    }
    public function getCodGestao()     { return $this->inCodGestao;        }
    public function getCodModulo()     { return $this->inCodModulo;        }
    public function getFormatoFolha()  { return $this->stFormatoFolha;     }
    public function getTipoSaida()     { return $this->stTipoSaida;        }
    public function getCodEntidades()  { return $this->stCodEntidades;     }
    public function getDataInicio()    { return $this->stDataInicio;       }
    public function getDataFinal()     { return $this->stDataFinal;        }

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        $this->setIndiceLista(0);
    }

    /**
        * Insere um objeto do tipo RecordSet para ser listado
        * @access Public
        * @param  Object $rsRecordSet Objeto a ser listado
        * @param  Object $boQuebraLinha adiciona quebra de linha ao início da lista
        * @param  Object $boQuebraPagina adiciona quebra de página ao início da lista
        * @param  Object $stClasse classe da lista
    */
    public function addLista($rsRecordSet, $boQuebraPagina = FALSE, $stQuebraLinha = "4mm", $stClasse = "border")
    {
        $inCountLista = count( $this->getRecordSet() );
        
        $this->setIndiceLista($inCountLista);

        $this->setRecordSet( $rsRecordSet );

        if($stQuebraLinha != '')
            $stQuebraLinha = "margin-top: ".$stQuebraLinha;

        $this->setQuebraLinha ( $inCountLista, $stQuebraLinha  );
        $this->setQuebraPagina( $inCountLista, $boQuebraPagina );
        $this->setClasse      ( $inCountLista, $stClasse       );
    }

    public function addMascara($stMascara, $stValor)
    {
        switch ($stMascara) {
            case "NUMERIC_BR":
                if(is_null($stValor) || strlen($stValor) == 0 || $stValor == 0 || $stValor == '' || !isset($stValor))
                    $stValor = '0,00';
                else
                    $stValor = number_format( $stValor, 2, ',', '.' );
            break;

            case "NUMERIC_BR_4":
                if(is_null($stValor) || strlen($stValor) == 0 || $stValor == 0 || $stValor == '' || !isset($stValor))
                    $stValor = '0,0000';
                else
                    $stValor = number_format( $stValor, 4, ',', '.' );
            break;
        }

        return $stValor;
    }

    /**
        * Insere um objeto Cabecalho na lista atual
        * LIMITE, RECOMENDADO, DE 5
        * @access Public
        * @param  Object $stRotulo título do cabeçalho
        * @param  Object $stAlinhamento alinhamento do cabeçalho. C -> Center, L -> Left, R -> Right
        * @param  Object $stWidth largura
        * @param  Object $inColspan quantidade de colunas a ocupar pelo cabeçalho
        * @param  Object $inRowspan quantidade de linhas a ocupar pelo cabeçalho
        * @param  Object $stStyle estilo do cabeçalho
        * @param  Object $stQuebraLinha FALSE -> não quebra linha, TRUE -> quebra linha antes de iniciar o cabeçalho
        * @param  Object $stMascara função para mascarar o título do cabeçalho.
        * * NUMERIC_BR -> resultado final 0.000,00
        * * NUMERIC_BR_4 -> resultado final 0.000,0000
        * @param  Object $stClasse classe do título do cabeçalho. Padrão cabecalho_geral border
    */
    public function addCabecalhoGeral($stRotulo, $stAlinhamento = "C",  $stWidth= "",  $inColspan = 1, $inRowspan = 1, $stStyle = "", $stQuebraLinha = 'FALSE', $stMascara = "", $stClasse = "cabecalho_geral border font_size_10")
    {
        $arCabecalhoGeralNovo = array( "rotulo"        => $stRotulo
                                     , "alinhamento"   => $stAlinhamento
                                     , "width"         => $stWidth
                                     , "colspan"       => $inColspan
                                     , "rowspan"       => $inRowspan
                                     , "style"         => $stStyle
                                     , "boQuebraLinha" => $stQuebraLinha
                                     , "mascara"       => $stMascara
                                     , "class"         => $stClasse
                                     );

        $this->setCabecalhoGeral($arCabecalhoGeralNovo);
    }
    
    /**
        * Insere um objeto Cabecalho na lista atual
        * @access Public
        * @param  Object $stRotulo título do cabeçalho
        * @param  Object $stAlinhamento alinhamento do cabeçalho. C -> Center, L -> Left, R -> Right
        * @param  Object $stWidth largura
        * @param  Object $inColspan quantidade de colunas a ocupar pelo cabeçalho
        * @param  Object $inRowspan quantidade de linhas a ocupar pelo cabeçalho
        * @param  Object $stStyle estilo do cabeçalho
        * @param  Object $stQuebraLinha FALSE -> não quebra linha, TRUE -> quebra linha antes de iniciar o cabeçalho
        * @param  Object $stMascara função para mascarar o título do cabeçalho.
        * * NUMERIC_BR -> resultado final 0.000,00
        * * NUMERIC_BR_4 -> resultado final 0.000,0000
        * @param  Object $stClasse classe do título do cabeçalho. Padrão border
    */
    public function addCabecalho($stRotulo, $stAlinhamento = "C",  $stWidth= "",  $inColspan = 1, $inRowspan = 1, $stStyle = "", $stQuebraLinha = 'FALSE', $stMascara = "", $stClasse = "border")
    {
        $arCabecalhoNovo = array( "rotulo"        => $stRotulo
                                , "alinhamento"   => $stAlinhamento
                                , "width"         => $stWidth
                                , "colspan"       => $inColspan
                                , "rowspan"       => $inRowspan
                                , "style"         => $stStyle
                                , "boQuebraLinha" => $stQuebraLinha
                                , "mascara"       => $stMascara
                                , "class"         => $stClasse
                                );

        $this->setCabecalho($this->getIndiceLista(), $arCabecalhoNovo);
    }

    /**
        * Insere um objeto Rodape na lista atual
        * @access Public
        * @param  Object $stRotulo título ou descrição do rodape
        * @param  Object $stAlinhamento alinhamento do rodape. C -> Center, L -> Left, R -> Right
        * @param  Object $stWidth largura
        * @param  Object $inColspan quantidade de colunas a ocupar pelo rodape
        * @param  Object $inRowspan quantidade de linhas a ocupar pelo rodape
        * @param  Object $stStyle estilo do rodape
        * @param  Object $stMascara função para mascarar o título ou descrição do rodape.
        * * NUMERIC_BR -> retorno 0.000,00
        * * NUMERIC_BR_4 -> retorno 0.000,0000
        * @param  Object $stClasse classe do título ou descrição do rodape. Padrão border
    */
    public function addRodape($stRotulo, $stAlinhamento = "C", $stWidth = "",  $inColspan = 1, $inRowspan = 1, $stStyle = "", $stMascara = "", $stClasse = "border")
    {
        $arRodapeNovo = array( "rotulo"        => $stRotulo
                             , "alinhamento"   => $stAlinhamento
                             , "width"         => $stWidth
                             , "colspan"       => $inColspan
                             , "rowspan"       => $inRowspan
                             , "style"         => $stStyle
                             , "mascara"       => $stMascara
                             , "class"         => $stClasse
                             );

        $this->setRodape($this->getIndiceLista(), $arRodapeNovo);
    }

    /**
        * Insere um objeto Campo na lista atual
        * @access Public
        * @param  Object $stCampo descrição do campo
        * @param  Object $stAlinhamento alinhamento do campo. C -> Center, L -> Left, R -> Right
        * @param  Object $inColspan quantidade de colunas a ocupar pelo campo
        * @param  Object $inRowspan quantidade de linhas a ocupar pelo campo
        * @param  Object $stStyle estilo do campo
        * @param  Object $stMascara função para mascarar a descrição do campo.
        * * NUMERIC_BR -> retorno 0.000,00
        * * NUMERIC_BR_4 -> retorno 0.000,0000
        * @param  Object $stClasse classe a descrição do campo. Padrão border
    */
    public function addCampo($stCampo, $stAlinhamento = "L", $stWidth = "", $inColspan = 1, $inRowspan = 1, $stStyle = "", $stMascara = "", $stClasse = "border")
    {
        $arCampoNovo = array( "campo"       => $stCampo
                            , "alinhamento" => $stAlinhamento
                            , "width"       => $stWidth
                            , "colspan"     => $inColspan
                            , "rowspan"     => $inRowspan
                            , "style"       => $stStyle
                            , "mascara"     => $stMascara
                            , "class"       => $stClasse
                            );

        $this->setCampo($this->getIndiceLista(), $arCampoNovo);
    }

    /**
        * Monta o cabeçalho geral do html
        * @access Private
    */
    public function montaCabecalhoGeral()
    {
        $arListaCabecalhoGeral = $this->getCabecalhoGeral();

        $stHtmlCabecalhoGeral = "";
        $stHtmlCabecalhoGeralFinal = "";

        $stStyle = "";
        $stClasse = "";
        $inColspan = 0;
        $boQuebraLinha = FALSE;

        if(is_array($arListaCabecalhoGeral) && count($arListaCabecalhoGeral) > 0){
            $stHtmlCabecalhoGeral .= "<thead>";
            $stHtmlCabecalhoGeral .= "   <tr>";

            foreach ($arListaCabecalhoGeral as $arValue) {
                if($arValue["boQuebraLinha"] == 'TRUE'){
                    $stHtmlCabecalhoGeral .= "   </tr>";
                    $stHtmlCabecalhoGeral .= "   <tr>";
                    $boQuebraLinha = TRUE;
                }

                $stHtmlCabecalhoGeral .= "<td";

                $stAlinhamento = "";
                if($arValue["alinhamento"]==''||$arValue["alinhamento"]=='L')
                    $stAlinhamento = " text_align_left ";
                else if($arValue["alinhamento"]=='R')
                    $stAlinhamento = " text_align_right ";
                else if($arValue["alinhamento"]=='C')
                    $stAlinhamento = " text_align_center ";

                if($arValue["class"]!=''){
                    $stHtmlCabecalhoGeral .= " class=\"".$arValue["class"].$stAlinhamento."\"";
                    $stClasse = $arValue["class"];
                }
                else
                    $stHtmlCabecalhoGeral .= " class=\"".$stAlinhamento."\"";

                if($arValue["width"]!='')
                    $stHtmlCabecalhoGeral .= " width=\"".$arValue["width"]."\"";

                if($arValue["colspan"]!=''){
                    $stHtmlCabecalhoGeral .= " colspan=".$arValue["colspan"];
                    if(!$boQuebraLinha)
                        $inColspan += $arValue["colspan"];
                }else{
                    if(!$boQuebraLinha)
                        $inColspan++;
                }

                if($arValue["rowspan"]!='')
                    $stHtmlCabecalhoGeral .= " rowspan=\"".$arValue["rowspan"]."\"";

                if($arValue["style"]!=''){
                    $stHtmlCabecalhoGeral .= " style=\"".$arValue["style"]."\"";
                    $stStyle = $arValue["style"];
                }

                $stHtmlCabecalhoGeral .= " >";

                $stRotulo = $arValue["rotulo"];
                if($stRotulo == '')
                    $stRotulo = "&nbsp;";

                if($arValue["mascara"]!='')
                    $stRotulo = $this->addMascara($arValue["mascara"], $stRotulo);

                $stHtmlCabecalhoGeral .= $stRotulo;

                $stHtmlCabecalhoGeral .= "</td>";
            }

            $stHtmlCabecalhoGeral .= "   </tr>";
            $stHtmlCabecalhoGeral .= "</thead>";

            $stStyle .= "; margin-right:-1;";

            $stHtmlCabecalhoGeralFinal  = $this->abreTable( $stStyle, $stClasse );
            $stHtmlCabecalhoGeralFinal .= $stHtmlCabecalhoGeral;
            $stHtmlCabecalhoGeralFinal .= $this->fechaTable();
        }

        $this->setHtmlCabecalhoGeral($stHtmlCabecalhoGeralFinal);
    }

    /**
        * Monta o cabeçalho da lista corrente
        * @access Private
        * @param  $inIndiceLista Determina o indice da lista corrente
    */
    public function montaCabecalhoLista($inIndiceLista)
    {
        $arListaCabecalho = $this->getCabecalho($inIndiceLista);

        $stHtmlCabecalho = "";

        if(is_array($arListaCabecalho)){
            $stHtmlCabecalho .= "<thead>";
            $stHtmlCabecalho .= "   <tr>";

            foreach ($arListaCabecalho as $arValue) {
                if($arValue["boQuebraLinha"] == 'TRUE'){
                    $stHtmlCabecalho .= "   </tr>";
                    $stHtmlCabecalho .= "   <tr>";
                }

                $stHtmlCabecalho .= "<td";

                $stAlinhamento = "";
                if($arValue["alinhamento"]==''||$arValue["alinhamento"]=='L')
                    $stAlinhamento = " text_align_left ";
                else if($arValue["alinhamento"]=='R')
                    $stAlinhamento = " text_align_right ";
                else if($arValue["alinhamento"]=='C')
                    $stAlinhamento = " text_align_center ";

                if($arValue["class"]!='')
                    $stHtmlCabecalho .= " class=\"".$arValue["class"].$stAlinhamento."\"";
                else
                    $stHtmlCabecalho .= " class=\"".$stAlinhamento."\"";

                if($arValue["width"]!='')
                    $stHtmlCabecalho .= " width=\"".$arValue["width"]."\"";

                if($arValue["colspan"]!='')
                    $stHtmlCabecalho .= " colspan=".$arValue["colspan"];

                if($arValue["rowspan"]!='')
                    $stHtmlCabecalho .= " rowspan=\"".$arValue["rowspan"]."\"";

                if($arValue["style"]!='')
                    $stHtmlCabecalho .= " style=\"".$arValue["style"]."\"";

                $stHtmlCabecalho .= " >";

                $stRotulo = $arValue["rotulo"];
                if($stRotulo == '')
                    $stRotulo = "&nbsp;";

                if($arValue["mascara"]!='')
                    $stRotulo = $this->addMascara($arValue["mascara"], $stRotulo);

                $stHtmlCabecalho .= $stRotulo;

                $stHtmlCabecalho .= "</td>";
            }

            $stHtmlCabecalho .= "   </tr>";
            $stHtmlCabecalho .= "</thead>";
        }

        return $stHtmlCabecalho;
    }

    /**
        * Monta o rodape da lista corrente
        * @access Private
        * @param  $inIndiceLista Determina o indice da lista corrente
    */
    public function montaRodapeLista($inIndiceLista)
    {
        $stHtmlRodape = "";
        $arListaRodape = $this->getRodape($inIndiceLista);

        if(is_array($arListaRodape)){
            $stHtmlRodape .= "<tfoot>";
            $stHtmlRodape .= "   <tr>";

            foreach ($arListaRodape as $arValue) {
                $stHtmlRodape .= "<td";

                $stAlinhamento = "";
                if($arValue["alinhamento"]==''||$arValue["alinhamento"]=='L')
                    $stAlinhamento = " text_align_left ";
                else if($arValue["alinhamento"]=='R')
                    $stAlinhamento = " text_align_right ";
                else if($arValue["alinhamento"]=='C')
                    $stAlinhamento = " text_align_center ";

                if($arValue["class"]!='')
                    $stHtmlRodape .= " class=\"".$arValue["class"].$stAlinhamento."\"";
                else
                    $stHtmlRodape .= " class=\"".$stAlinhamento."\"";

                if($arValue["width"]!='')
                    $stHtmlRodape .= " width=\"".$arValue["width"]."\"";

                if($arValue["colspan"]!='')
                    $stHtmlRodape .= " colspan=".$arValue["colspan"];

                if($arValue["rowspan"]!='')
                    $stHtmlRodape .= " rowspan=\"".$arValue["rowspan"]."\"";

                if($arValue["style"]!='')
                    $stHtmlRodape .= " style=\"".$arValue["style"]."\"";

                $stHtmlRodape .= " >";

                $stRotulo = $arValue["rotulo"];
                if($stRotulo == '')
                    $stRotulo = "&nbsp;";

                if($arValue["mascara"]!='')
                    $stRotulo = $this->addMascara($arValue["mascara"], $stRotulo);

                $stHtmlRodape .= $stRotulo;

                $stHtmlRodape .= "</td>";
            }

            $stHtmlRodape .= "   </tr>";
            $stHtmlRodape .= "</tfoot>";
        }

        return $stHtmlRodape;
    }

    /**
        * Monta o campo da lista corrente
        * @access Private
        * @param  $inIndiceLista Determina o indice da lista corrente
    */
    public function montaCampoLista($inIndiceLista)
    {
        $arRecordSet  = $this->getRecordSetIndice($inIndiceLista);
        $arListaCampo = $this->getCampo($inIndiceLista);

        $stHtmlCampo = "";
        $inCountLinha = 1;

        $rsRecordSet = new RecordSet;
        if ($arRecordSet == '' || $arRecordSet == null) {
            $rsRecordSet->preenche($rsIndiceLista);
        } else {
            $rsRecordSet = $arRecordSet;
        }

        if($rsRecordSet->getNumLinhas() > 0 && is_array($arListaCampo)){
            $stHtmlCampo .= "<tbody>";

            while ( !$rsRecordSet->eof()  ) {
                $stHtmlCampo .= "   <tr>";

                foreach ($arListaCampo as $arValue) {
                    $stHtmlCampo .= "       <td";

                    $stAlinhamento = "";
                    if($arValue["alinhamento"]==''||$arValue["alinhamento"]=='L')
                        $stAlinhamento = " text_align_left ";
                    else if($arValue["alinhamento"]=='R')
                        $stAlinhamento = " text_align_right ";
                    else if($arValue["alinhamento"]=='C')
                        $stAlinhamento = " text_align_center ";

                    if($arValue["class"]!='')
                        $stHtmlCampo .= " class=\"".$arValue["class"].$stAlinhamento."\"";
                    else
                        $stHtmlCampo .= " class=\"".$stAlinhamento."\"";

                    if($arValue["width"]!='')
                        $stHtmlCampo .= " width=\"".$arValue["width"]."\"";

                    if($arValue["colspan"]!='')
                        $stHtmlCampo .= " colspan=".$arValue["colspan"];

                    if($arValue["rowspan"]!='')
                    $stHtmlCabecalho .= " rowspan=\"".$arValue["rowspan"]."\"";

                    if($arValue["style"]!='')
                        $stHtmlCampo .= " style=\"".$arValue["style"]."\"";

                    $stCampoId = $arValue["campo"];
                    $stCampo = "";

                    if($stCampoId == '')
                        $stCampo = "&nbsp;";
                    else if (strstr($stCampoId,'[') || strstr($stCampoId,']')) {
                        for ($inCount=0; $inCount<strlen($stCampoId); $inCount++) {
                            if ($stCampoId[ $inCount ] == '[') $inInicialId = $inCount;
                            if (($stCampoId[ $inCount ] == ']') && isset($inInicialId) ) {
                                $stCampo .= $rsRecordSet->getCampo(trim( substr($stCampoId,$inInicialId+1,(($inCount-$inInicialId)-1))));
                                unset($inInicialId);
                            }elseif( !isset($inInicialId) )
                                $stCampo .= $stCampoId[ $inCount ];
                        }
                    } else {
                        $stCampo = $rsRecordSet->getCampo($stCampoId);
                    }

                    if($arValue["mascara"]!='')
                        $stCampo = $this->addMascara($arValue["mascara"], $stCampo);

                    $stHtmlCampo .= " >";
                    $stHtmlCampo .= $stCampo;
                    $stHtmlCampo .= " </td>";
                }

                $stHtmlCampo .= "   </tr>";

                $rsRecordSet->proximo();
            }

            $stHtmlCampo .= "</tbody>";
        }

        return $stHtmlCampo;
    }

    /**
        * Monta a assinatura da lista corrente
        * @access Private
        * @param  $inIndiceLista Determina o indice da lista corrente
    */
    public function montaAssinatura( $inIndiceLista, $rsAssinatura)
    {
        $stPaginaAssinatura = "";
        $arAssinaturas = array();

        foreach ($rsAssinatura as $key => $assinatura) {
            $arAssinaturas[] = $rsAssinatura[$key]->getElementos();
        }

        foreach ($arAssinaturas as $key => $assinatura) {
            $stPaginaAssinatura .= $this->abreTable( "margin-top: 20mm" );
            $stPaginaAssinatura .= "<tbody>";

            foreach ($assinatura as $chave => $campo) {
                if(count($arAssinaturas)>1)
                    $width = 33;
                else{
                    if(count($campo) == 3)
                        $width = 33;
                    else if(count($campo) == 2)
                        $width = 50;
                    else
                        $width = 100;
                }

                $stPaginaAssinatura .= "<tr>";

                foreach ($campo as $linha => $texto) {
                    if($chave==0)
                        $texto = str_pad($texto, 70, '_', STR_PAD_LEFT);

                    $stPaginaAssinatura .= "<td class=\"text_align_center\" width=\"".$width."%\" > ".$texto." </td>";

                    if(count($campo) == 2 && $linha == 1 && $width == 33)
                        $stPaginaAssinatura .= "<td class=\"text_align_center\" width=\"".$width."%\" > &nbsp; </td>";
                    else if(count($campo) == 1 && $width == 33){
                        $stPaginaAssinatura .= "<td class=\"text_align_center\" width=\"".$width."%\" > &nbsp; </td>";
                        $stPaginaAssinatura .= "<td class=\"text_align_center\" width=\"".$width."%\" > &nbsp; </td>";
                    }
                }

                $stPaginaAssinatura .= "</tr>";
            }

            $stPaginaAssinatura .= "</tbody>";
            $stPaginaAssinatura .= $this->fechaTable();
        }

        if( $stPaginaAssinatura != '' && count($this->getFiltro()) > 0 )
            $stPaginaAssinatura .= $this->addQuebraPagina();

        $this->setPagina($inIndiceLista, $stPaginaAssinatura);
    }

    /**
        * Monta o filtro da lista corrente
        * @access Private
        * @param  $inIndiceLista Determina o indice da lista corrente
    */
    public function montaFiltro( $inIndiceLista, $arMontaFiltro)
    {
        $stPaginaFiltro = "";

        if(count($arMontaFiltro) > 0){
            $stPaginaFiltro .= $this->abreTable( "margin-top: 4mm" );
            $stPaginaFiltro .= "<tbody>";
            $stPaginaFiltro .= "<tr>";
            $stPaginaFiltro .= "<td class=\"text_align_left\" > Filtro(s) utilizado(s) </td>";
            $stPaginaFiltro .= "<td class=\"text_align_left\" > </td>";
            $stPaginaFiltro .= "</tr>";
        }

        foreach ($arMontaFiltro as $chave => $value) {
            $stPaginaFiltro .= "<tr>";
            $stPaginaFiltro .= "<td class=\"text_align_left\" width=\"12%\" >   ".$value['titulo']." </td>";
            $stPaginaFiltro .= "<td class=\"text_align_left\"               > : ".$value['valor']."  </td>";
            $stPaginaFiltro .= "</tr>";
        }

        if(count($arMontaFiltro) > 0){
            $stPaginaFiltro .= "</tbody>";
            $stPaginaFiltro .= $this->fechaTable();
        }

        $this->setPagina($inIndiceLista, $stPaginaFiltro);
    }

    /**
        * Insere um objeto do tipo Array para ser listado em Assinatura
        * @access Public
        * @param  Object $rsAssinaturas Objeto a ser listado
    */
    public function addAssinatura($rsAssinaturas)
    {
        $this->setAssinatura($rsAssinaturas);
    }

    /**
        * Insere um objeto do tipo Array para ser listado em Filtro
        * @access Public
        * @param  Object $stTitulo titulo do filtro
        * @param  Object $stDescricao descrição do filtro
    */
    public function addFiltro( $stTitulo, $stDescricao )
    {
        $arMontaFiltro = array();
        $arMontaFiltro['titulo'] = $stTitulo;
        $arMontaFiltro['valor']  = $stDescricao;
        $this->setFiltro($arMontaFiltro);
    }

    /**
        * Abre o HTML da Table
        * @access Public
    */
    public function abreTable($stStyle = '', $stClasse = '', $stWidth = '')
    {
        $abreTabela  = "<table";

        if($stClasse != '')
            $abreTabela .= " class=\"".$stClasse."\"";

        if($stWidth == '')
            $stWidth = "100%";

        $abreTabela .= " width=\"".$stWidth."\"";

        if($stStyle != '')
            $abreTabela .= " style=\"".$stStyle."\"";

        $abreTabela .= " >";
        
        return $abreTabela;
    }

    /**
        * Fecha o HTML da Table
        * @access Public
    */
    public function fechaTable( )
    {
        $fechaTabela = "</table>";

        return $fechaTabela;
    }

    /**
        * Quebra a Página HTML
        * @access Public
    */
    public function addQuebraPagina()
    {
        $stQuebraPagina  = "<pagebreak />";

        return $stQuebraPagina;
    }

    /**
        * Monta a Página HTML conforme as propriedades setadas
        * @access Public
    */
    public function addPagina($inIndiceLista, $stQuebraLinha = '', $boQuebraPagina = '', $stClasse = '')
    {
        $stPagina  = "";

        $stStyle = "";
        if($stQuebraLinha != '')
            $stStyle .= $stQuebraLinha;

        if($boQuebraPagina != '' && $this->getPaginas()){
            $arPaginas = $this->getPaginas();
            foreach ($arPaginas as $inIndice => $stHtmlPagina) {
                if($stHtmlPagina != '')
                    $boPaginasAnteriores = TRUE;
            }

            if($boPaginasAnteriores)
                $stPagina .= $this->addQuebraPagina();
        }

        $stPagina .= $this->abreTable( $stStyle, $stClasse );
        $stPagina .= $this->montaCabecalhoLista( $inIndiceLista );
        $stPagina .= $this->montaCampoLista( $inIndiceLista );
        $stPagina .= $this->montaRodapeLista( $inIndiceLista );
        $stPagina .= $this->fechaTable();

        $this->setPagina($inIndiceLista, $stPagina);
    }
    
    /**
        * Monta a Página HTML conforme as propriedades setadas
        * @access Public
    */
    public function addPaginaSemResultado($inIndiceLista)
    {
        $stPagina  = $this->abreTable();
        $stPagina .= "<tr><td class=\"text_align_center font_size_11 tr_nivel_4\" >Nenhum Resultado Localizado</td></tr>";
        $stPagina .= $this->fechaTable();

        $this->setPagina($inIndiceLista, $stPagina);
    }

    /**
        * Monta o HTML conforme as propriedades setadas
        * @access Public
    */
    public function montaHTML()
    {
        $inUltimoIndiceLista = 0;

        foreach ($this->getRecordSet() as $inIndiceLista => $rsIndiceLista) {
            $this->addPagina( $inIndiceLista, $this->getQuebraLinha($inIndiceLista), $this->getQuebraPagina($inIndiceLista), $this->getClasse($inIndiceLista) );

            $inUltimoIndiceLista = $inIndiceLista;
        }

        if( count($this->getPaginas()) > 0 && is_array($this->getAssinatura()) && count($this->getAssinatura()) > 0 ){
            $inUltimoIndiceLista++;
            $this->montaAssinatura( $inUltimoIndiceLista, $this->getAssinatura() );
        }

        if( count($this->getPaginas()) == 0 ){
            $inUltimoIndiceLista++;
            $this->addPaginaSemResultado( $inUltimoIndiceLista );
        }

        if( count($this->getFiltro()) > 0 ){
            $inUltimoIndiceLista++;
            $this->montaFiltro( $inUltimoIndiceLista, $this->getFiltro() );
        }

        $stHtml = '';
        $arPaginasHtml = $this->getPaginas();
        foreach ($arPaginasHtml as $inIndiceLista => $stHtmlTable) {
            $stHtml .= $stHtmlTable;
        }

        $this->setHtmlLista($stHtml);

        if(count($this->getCabecalhoGeral()) > 0)
            $this->montaCabecalhoGeral();
    }

    /**
        * retorna o HTML
        * @access Public
    */
    public function getHTML()
    {
        return $this->getHtmlLista();
    }

    /**
        * @method geraRelatorioMPDF
        * Metodo para gerar o relatório em PDF na classe mpdf
    */
    public function geraRelatorioMPDF()
    {
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
        include_once CLA_MPDF;

        $this->obMPDF = new FrameWorkMPDF($this->getCodGestao(),$this->getCodModulo());
        $this->obMPDF->setCodEntidades ( $this->getCodEntidades()   );
        $this->obMPDF->setDataInicio   ( $this->getDataInicio()     );
        $this->obMPDF->setDataFinal    ( $this->getDataFinal()      );
        $this->obMPDF->setNomeRelatorio( $this->getNomeRelatorio()  );
        $this->obMPDF->setFormatoFolha ( $this->getFormatoFolha()   );
        $this->obMPDF->setTipoSaida    ( $this->getTipoSaida()      );

        $this->montaHTML();
        $stHtml = $this->getHTML();

        $stHtmlListaCabecalho = $this->getHtmlCabecalhoGeral();

        $this->obMPDF->gerarRelatorio( $stHtml, $stHtmlListaCabecalho );
    }
}
?>
