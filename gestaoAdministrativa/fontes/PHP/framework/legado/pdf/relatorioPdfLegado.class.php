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

class relatorioPdfLegado
{
/**************************************************************************/
/**** Propriedades Públicas                                             ***/
/**************************************************************************/
    public $sSQL,
        $sTitulo,
        $sSubTitulo,
        $sFilaImpressao,
        $iCopias,
        $sAcaoPDF,
        $sScriptFile,
        $BANCODEDADOS,
        $PROPRIEDADES,
        $sParametros;

/**************************************************************************/
/**** Propriedades Privadas                                             ***/
/**************************************************************************/
    public $PDF,
        $dbPDF,
        $xml_parser,
        $detalhe,
        $detalheStatus,
        $fonte,
        $aSQL,
        $iContaSQL,
        $iContaDet,
        $iContaNivel,
        $aNivelSup,
        $aParametros,
        $bPrintTexto,
        $aTextoDados,
        $PageFormat;

/**************************************************************************/
/**** Método Construtor                                                 ***/
/**************************************************************************/
    public function relatorioPdfLegado()
    {
        $this->aSQL          = array();
        $this->aListaDB      = array();
        $this->detalheStatus = array();
        $this->aParametros   = array();
        $this->iContaDet     = -1;
        $this->iContaSQL     = -1;
        $this->iContaNivel   = -1;
        $this->aNivelSup[0]  = -1;
        $this->bPrintTexto   = false;
        $this->aTextoDados   = array();
        $this->PageFormat    = "a4";
    }
/**************************************************************************/
/**** Métodos Públicos                                                  ***/
/**************************************************************************/
   public function abreScript($sFile)
   {
        if (strlen($sFile)>4) {
            $iPos=strrpos($sFile,'.');
            if (!$iPos) {
                exit('Arquivo de script de relatório não tem extensão: '.$sFile);
            } else {
                if (strtolower(substr($sFile,$iPos+1,3))=='xml') {
                    $this->sScriptFile = $sFile;
                } else {
                    exit('Arquivo de script de relatório não é xml: '.$sFile);
                }
            }
        } else {
            exit('Arquivo de script de relatório não foi informado!');
        }
    }

    public function carregaDados()
    {
        $this->xml_parser = xml_parser_create();
        xml_set_object($this->xml_parser,$this);
        xml_set_element_handler($this->xml_parser, "elementoInicio", "elementoFim");

        if (!($fp = fopen($this->sScriptFile, "r"))) {
            exit("Não foi possível abrir o script XML!");
        }

        while ($data = fread($fp, 4096)) {
            if (!xml_parse($this->xml_parser, $data, feof($fp))) {
                exit(sprintf("Erro no XML: %s na linha %d",
                xml_error_string(xml_get_error_code($this->xml_parser)),
                xml_get_current_line_number($this->xml_parser)));
            }
        }
        xml_parser_free($this->xml_parser);
    }

    public function imprimePDF()
    {
        $this->montaCabecalhoHTML();

        $this->iCopias = (int) $this->iCopias;
        if ($this->iCopias<1) {
            $this->iCopias=1;
        }
        $sParams = '';
        if ($this->PDF->DefOrientation=='L') {
            $sParams .= '-landscape ';
        }

        $sFile = CAM_FRAMEWORK."tmp/doc_".date("Y-m-d",time()).'_'.date("His",time()).'_'.substr(Sessao::getId(),10,6);
        $sFilePDF = $sFile.".pdf";
        $sFilePS  = $sFile.".ps";
        $this->PDF->Output($sFilePDF);

        $cmdo  = "lpr -P".$this->sFilaImpressao." -#".$this->iCopias;
        $cmdo .= " < /usr/bin/gs -sDEVICE=psgray -sPAPERSIZE=a4 -sOutputFile=".$sFilePS." ".$sFilePDF;

        $aAux = array();
        exec($cmdo, $aAux);
        exec("rm $sFilePDF", $aAux);
        print '
        <div align="center">
        <table width="500">
            <tr>
                <td class="label">Título:</td>
                <td class="field">'.$this->PDF->sTitulo.'&nbsp;</td>
            </tr>
            <tr>
                <td class="label">Subtítulo:</td>
                <td class="field">'.$this->PDF->sSubTitulo.'&nbsp;</td>
            </tr>
            <tr>
                <td class="label">Script XML:</td>
                <td class="field">'.$this->PDF->sNomeRelatorio.'&nbsp;</td>
            </tr>
            <tr>
                <td class="label">Fila de Impressão:</td>
                <td class="field">'.$this->sFilaImpressao.'&nbsp;</td>
            </tr>
            <tr>
                <td class="label">Emissão:</td>
                <td class="field">'.$this->PDF->sData.' às '.$this->PDF->sHora.'</td>
            </tr>
            <tr>
                <td class="label">Número de Cópias:</td>
                <td class="field">'.$this->iCopias.'&nbsp;</td>
            </tr>
<!--
            <tr>
                <td class="label">Comando:</td>
                <td class="field">'.$cmdo.'&nbsp;</td>
            </tr>
-->
        </table>
        </div>
        <script type="text/javascript" src=ifuncoesJs.js></script>
        <script type="text/javascript">
            alertaAvisoNivel2("Documento enviado para a Fila de Impressão","unica","aviso",'.Sessao::getId().');
        </script>';
        $this->montaRodapeHTML();
    }

    public function salvaPDF()
    {
        $stNomeArquivo = "doc_".hoje(true).'_'.agora(true).'_'.substr(Sessao::getId(),10,6).'.pdf';
        $this->PDF->Output($stNomeArquivo,'D');
    }

    public function enviaPDF()
    {
        $sFile = "../../tmp/doc_".hoje(true).'_'.agora(true).'_'.substr(Sessao::getId(),10,6).'.pdf';
        $this->PDF->Output($sFile);
        exec("rm $sFile", $aAux);
    }

/**************************************************************************/
/**** Métodos Privados                                                  ***/
/**************************************************************************/

    public function elementoInicio($parser, $nome, $atributos)
    {
        switch ($nome) {
            case 'PROPRIEDADES':
                $this->propriedadesPDF($nome,$atributos);
                break;
            case 'MARGENS':
                $this->margensPDF($nome,$atributos);
                break;
            case 'CORPO':
                $this->corpoPDF($nome,$atributos);
                break;
            case 'GRUPO':
                break;
            case 'CABECALHO':
                break;
            case 'QUEBRALINHA':
                $this->quebraLinhaPDF($nome,$atributos);
                break;
            case 'QUEBRAPAGINA':
                $this->quebraPaginaPDF($nome,$atributos);
                break;
            case 'DETALHE':
                $this->detalhePDF('abre',$atributos);
                break;
            case 'ETIQUETA':
                $this->etiquetaPDF($nome,$atributos);
                break;
            case 'CAMPO':
                $this->campoPDF($nome,$atributos);
                break;
            case 'CAMPOTEXTO':
                $this->campoTextoPDF($nome,$atributos);
                break;
            case 'FONTE':
                $this->fontePDF($nome,$atributos,'abre');
                break;
            case 'PARAMETRO':
                $this->parametroPDF($nome,$atributos);
                break;
            case 'PARAMETROTEXTO':
                $this->parametroTextoPDF($nome,$atributos);
                break;
        }
    }

    public function elementoFim($parser, $nome)
    {
        switch ($nome) {
            case 'DETALHE':
                $this->detalhePDF('fecha',$atributos);
                break;
            case 'FONTE':
                $this->fontePDF($nome,$atributos,'fecha');
                break;
        }
    }

    public function propriedadesPDF($nome,$aAtributos)
    {
        if (strlen($this->PROPRIEDADES["TITULO"])>6) {
            $aAtributos["TITULO"] = $this->PROPRIEDADES["TITULO"];
        }
        if (strlen($this->PROPRIEDADES["SUBTITULO"])>6) {
            $aAtributos["SUBTITULO"] = $this->PROPRIEDADES["SUBTITULO"];
        }
        $this->PDF = new arquivoPdf($aAtributos["DISPOSICAO"],'mm',$aAtributos["FORMATO"]);
        $this->PDF->sModulo       = $aAtributos["MODULO"];
        $this->PDF->sTitulo       = $aAtributos["TITULO"];
        $this->PDF->sSubTitulo    = $aAtributos["SUBTITULO"];
        $this->PDF->SetAuthor($aAtributos["AUTOR"]);
        $this->PDF->SetSubject($aAtributos["ASSUNTO"]);
        $this->PDF->SetTitle($aAtributos["TITULO"]);
        $this->PDF->SetKeywords($aAtributos["PALAVRASCHAVE"]);
        $this->PDF->sUsuario = Sessao::getUsername();
        $this->PDF->SetCreator('URBEM - CNM Confederação Nacional de Municípios - www.cnm.org.br');
        $this->aSQL = explode(';',$this->BANCODEDADOS["SQL"]);
        if (strlen(trim($this->sParametros))>0) {
            $this->trataParametros($this->sParametros);
        }
    }

    public function trataParametros($sPar)
    {
        $iPos = strpos($sPar,";");
        if ($iPos<1) {
            $sPar .= ";";
        }
        $aAux = explode(";",$sPar);
        if (is_array($aAux)) {
            while (list($key,$val)=each($aAux)) {
                $aPar = explode("=",$val);
                if (is_array($aPar)) {
                    $sIdx = $aPar[0];
                    $sVlr = $aPar[1];
                    $this->aParametros["$sIdx"] = $sVlr;
                }
            }
        }
    }

    public function margensPDF($nome,$atributos)
    {
        $iMargemSup = 0;
        $iMargemEsq = 0;
        $iMargemDir = 0;
        /* deferá ser verificada a configuração do lpr
        if ($this->sAcaoPDF=='imprimir') {
            if ($this->PDF->DefOrientation=='P') {
                $iMargemSup = 12;
                $iMargemEsq = 5;
                $iMargemDir = 15;
            } else {
                $iMargemSup = 5;
                $iMargemEsq = 5;
                $iMargemDir = 15;
            }
        }
        */
        if ($atributos["MARGEMSUP"] < 15) {
            $atributos["MARGEMSUP"] = 15;
        }
        $this->PDF->SetTopMargin  ($atributos["MARGEMSUP"]);
        $this->PDF->SetLeftMargin ($atributos["MARGEMESQ"]);
        $this->PDF->SetRightMargin($atributos["MARGEMDIR"]);
    }

    public function trataSQLGrupo($sSQLLocal)
    {
        $sSQLLocal = str_replace('&',"'\".\$this->chave[",$sSQLLocal); //"
        $iTam      = strlen($sSQLLocal);
        $i    = 0;
        $sAux = "";
        $bAchouVar = false;
        while ($i<$iTam) {
            $cChar = substr($sSQLLocal,$i,1);
            $iChar = ord($cChar);
            if ($iChar==36) {
                $bAchouVar = true;
            }
            if ($bAchouVar and ($iChar==10 or $iChar==32 or $iChar==59)) {
                $sAux .= "].\"'";   //"'
                $bAchouVar = false;
            }
            $sAux .= $cChar;
            $i++;
        }
        if ($bAchouVar) {
            $sAux .= "].\"'";   //"'
            $bAchouVar = false;
        }

        return $sAux;
    }

    public function databasePDF()
    {
        $sSQLLocal = $this->detalhe[$this->iContaDet][0]["SQL"];

        if (strlen($sSQLLocal)>6) {
            $sSQLLocal = $this->trataSQLGrupo($sSQLLocal);
            $this->dbPDF[$this->iContaDet] = new dataBaseLegado();
            $this->dbPDF[$this->iContaDet]->abreBD();
            $sAux2 = "\$sAux = \"".str_replace('!','"',str_replace('@','.',$sSQLLocal))."\";";  //'
            eval($sAux2);
            $this->dbPDF[$this->iContaDet]->abreSelecao(stripslashes($sAux));
        }
    }

    public function corpoPDF($nome,$atributos)
    {
        $sMunic = pegaConfiguracao('nom_municipio');
        $this->PDF->sLogoPrefeitura = CAM_FW_IMAGENS.pegaConfiguracao('logotipo');
        $this->PDF->sNomePrefeitura = pegaConfiguracao('nom_prefeitura');
        $this->PDF->sEnderecoPrefeitura[0] = "Fone: ".pegaConfiguracao('ddd').pegaConfiguracao('fone');
        $this->PDF->sEnderecoPrefeitura[1] = "E-mail: ".pegaConfiguracao('e_mail');
        $this->PDF->sEnderecoPrefeitura[2] = pegaConfiguracao('tipo_logradouro')." ".
                                             pegaConfiguracao('logradouro')." ".
                                             pegaConfiguracao('numero');
        $this->PDF->sEnderecoPrefeitura[3]  = "Cep: ".pegaConfiguracao('cep')." - ".$sMunic;

        $this->PDF->sImprimeUsuario = strtoupper(pegaConfiguracao('usuario_relatorio'));
        $this->PDF->sNomeRelatorio  = $this->sScriptFile;
        $this->PDF->sData      = hoje();
        $this->PDF->sHora      = agora();
        $this->PDF->Open();
        $this->PDF->AliasNbPages();
        $this->PDF->AddPage();
        $this->fonte[] = array(NOME    => 'Helvetica',
                               ALTURA  => 8,
                               ESTILO  => '',
                               COR     => '0,0,0');
        $this->PDF->SetFont('Helvetica','',8);
        $this->PDF->SetFillColor(255,255,255);
        $this->PDF->SetTextColor(0,0,0);
    }

    public function detalhePDF($acao,$atributos)
    {
        if ($acao=='abre') {
            $this->iContaSQL++;
            if (is_array($atributos)) {
                $iNivel    = (int) $atributos["NIVEL"];
                if ($iNivel>0) {
                    $iNivelSup = (int) $this->aNivelSup[$iNivel];
                    if ($iNivelSup==0) {
                        $this->aNivelSup[$iNivel] = (int) $this->iContaDet;
                        $iNivelSup = (int) $this->iContaDet;
                    }
                    $this->detalhe[$iNivelSup][]["DETALHE"] = $this->iContaSQL;
                }
            }
            if ($iNivel>0) {
                $this->iContaDet = sizeof($this->detalhe);
            } else {
                $this->iContaDet++;
            }
            $this->iContaNivel++;
            $this->detalhe[$this->iContaDet] = array();
            $this->detalheStatus[$this->iContaDet] = $acao;
            if (strlen($this->aSQL[$this->iContaSQL])>6) {
                $atributos["SQL"] = $this->aSQL[$this->iContaSQL];
            }
            $this->detalhe[$this->iContaDet][0]["SQL"] = $atributos["SQL"];
            if (strlen($atributos["CHAVE"])>2) {
                $this->detalhe[$this->iContaDet][]["CHAVE"] = explode(",",$atributos["CHAVE"]);
            }
            //Verifica se há solicitação de cores alternadas
            if (array_key_exists('ALTERNAR',$atributos)) {
                $this->alternar = $atributos['ALTERNAR'];
                if (strlen($atributos['CORALTERNADA1'])>0) {
                    $this->corAlternada1 = $atributos['CORALTERNADA1'];
                } else {
                    $this->corAlternada1 = "255,255,255";
                }
                if (strlen($atributos['CORALTERNADA2'])>0) {
                    $this->corAlternada2 = $atributos['CORALTERNADA2'];
                } else {
                    $this->corAlternada2 = "255,255,255";
                }
                $this->corAtual = $this->corAlternada1;
                $this->altCont = 0;
            } else {
                $this->alternar = 0;
            }
        } else {

            $this->detalheStatus[$this->iContaDet] = $acao;
            if ($this->iContaNivel>0) {
                $this->iContaDet = $this->aNivelSup[$this->iContaNivel];
            } else {
                $this->iContaDet--;
            }
            $this->iContaNivel--;
            if ($this->iContaNivel<0) {
                $this->iContaDet = -1;
                $this->imprimeDetalhePDF(0);
                $this->iContaDet = -1;
            }
        }
    }

    public function imprimeDetalhePDF($iDet)
    {
        $this->iContaDet = (int) $iDet;
        $this->detalheStatus[$this->iContaDet] = 'fecha';
        $this->databasePDF();

        // Colocado para testes --> Testar e Verificar se realmente é necessário! 08/01/2009
        if ($this->dbPDF != '') {

            $this->dbPDF[$this->iContaDet]->vaiPrimeiro();
            while (!$this->dbPDF[$this->iContaDet]->eof()) {
                if ($this->alternar > 0) {
                    if ($this->altCont == $this->alternar) {
                        $this->altCont = 0;
                        if ($this->corAtual == $this->corAlternada1) {
                            $this->corAtual = $this->corAlternada2;
                        } else {
                            $this->corAtual = $this->corAlternada1;
                        }
                    }
                    $this->altCont++;
                }
                reset($this->detalhe[$this->iContaDet]);
                while (list($key,$val)=each($this->detalhe[$this->iContaDet])) {

                    while (list($nome,$atributos)=each($val)) {
                        if (connection_status()==1) {
                            exit("Operação abortada pelo usuario!");
                        } elseif (connection_status()==2) {
                            exit("Operação abortada por tempo de execução expirado!");
                        }
                        switch ($nome) {
                            case 'CHAVE':
                                if (is_array($atributos)) {
                                    while (list($k,$v)=each($atributos)) {
                                        $v = strtolower(trim($v));
                                        $this->chave[$v] = $this->dbPDF[$this->iContaDet]->pegaCampo($v);
                                    }
                                }
                                break;
                            case 'QUEBRALINHA':
                                $this->quebraLinhaPDF($nome,$atributos);
                                break;
                            case 'QUEBRAPAGINA':
                                $this->quebraPaginaPDF($nome,$atributos);
                                break;
                            case 'DETALHE':
                                $iAux = $this->iContaDet;
                                $this->imprimeDetalhePDF($atributos);
                                $this->iContaDet = $iAux;
                                break;
                            case 'ETIQUETA':
                                $this->etiquetaPDF($nome,$atributos);
                                break;
                            case 'CAMPO':
                                $this->campoPDF($nome,$atributos);
                                break;
                            case 'CAMPOTEXTO':
                                $this->campoTextoPDF($nome,$atributos);
                                break;
                            case 'PARAMETRO':
                                $this->parametroPDF($nome,$atributos);
                                break;
                            case 'PARAMETROTEXTO':
                                $this->parametroTextoPDF($nome,$atributos);
                                break;
                            case 'FONTE_ABRE':
                                $this->fontePDF($nome,$atributos,'abre');
                                break;
                            case 'FONTE_FECHA':
                                $this->fontePDF($nome,$atributos,'fecha');
                                break;
                        }
                    }
                }
                $this->dbPDF[$this->iContaDet]->vaiProximo();
            }
            $this->dbPDF[$this->iContaDet]->limpaSelecao();
        }
    }




    public function imprimeTexto()
    {
        $atributos = $this->aTextoDados["atributos"];
        $lar = (int) $atributos["LARGURA"];
        $alt = (int) $atributos["ALTURA"];
        $bor = $atributos["BORDA"];
        $ali = $atributos["ALINHAMENTO"];
        $cfu = explode(',',$atributos["CORDEFUNDO"]);
        $fun = 1;   //forçar impressão do fundo mesmo que seja branco.
        if ($bor=='') {
            $bor='0';
        }
        if ($ali=='') {
            $ali='L';
        }
        if ($ali=='D') {
            $ali='R';
        }
        if ($ali=='E') {
            $ali='L';
        }
        if ($cfu[0]!='') {
            $fun = 1;
            $this->PDF->SetFillColor($cfu[0],$cfu[1],$cfu[2]);
        }
        $vlr  = $this->aTextoDados["texto"];
        $posX = $this->aTextoDados["posicaoX"];
        $this->PDF->SetX($posX);
        $this->PDF->multicell($lar,$alt,$vlr,$bor,$ali,$fun);
        if ($cfu[0]!='') {
            $fun = 0;
            $this->PDF->SetFillColor(255,255,255);
        }
        $this->aTextoDados = array();
    }



    public function quebraLinhaPDF($nome,$atributos)
    {
        $alt = (int) $atributos["ALTURA"];
        if ($alt>0) {
            if ($this->detalheStatus[$this->iContaDet]=='abre') {
                $this->detalhe[$this->iContaDet][][$nome] = $atributos;
            } else {
                if ($this->bPrintTexto) {
                    $this->imprimeTexto();
                    $this->bPrintTexto = false;
                } else {
                    $this->PDF->ln($alt);
                }
            }
        } else {
            if ($this->detalheStatus[$this->iContaDet]=='abre') {
                $this->detalhe[$this->iContaDet][][$nome] = $atributos;
            } else {
                if ($this->bPrintTexto) {
                    $this->imprimeTexto();
                    $this->bPrintTexto = false;
                } else {
                    $this->PDF->ln();
                }
            }
        }
    }

    public function quebraPaginaPDF($nome,$atributos)
    {
        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome] = $atributos;
        } else {
            $this->PDF->AddPage();
        }
    }

    public function etiquetaPDF($nome,$atributos)
    {
        $lar = (int) $atributos["LARGURA"];
        $alt = (int) $atributos["ALTURA"];
        $tit = $atributos["TITULO"];
        $bor = $atributos["BORDA"];
        $nli = (int) $atributos["NOVALINHA"];
        $ali = $atributos["ALINHAMENTO"];
        $cfu = explode(',',$atributos["CORDEFUNDO"]);
        $fun = 1;   //forçar impressão do fundo mesmo que seja branco.
        if ($bor=='') {
            $bor='0';
        }
        if ($ali=='') {
            $ali='L';
        }
        if ($ali=='D') {
            $ali='R';
        }
        if ($ali=='E') {
            $ali='L';
        }

        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome] = $atributos;
            if ($nli>0) {
                $this->detalhe[$this->iContaDet][]["QUEBRALINHA"] = array();
            }
        } else {
            if ($cfu[0]!='') {
                $fun = 1;
                $this->PDF->SetFillColor($cfu[0],$cfu[1],$cfu[2]);
            }
            $this->PDF->cell($lar,$alt,$tit,$bor,0,$ali,$fun,$lnk);
            if ($cfu[0]!='') {
                $fun = 0;
                $this->PDF->SetFillColor(255,255,255);
            }
        }
    }

    public function montaMascara($sMask, $vValor)
    {
        $sMask = trim(strtr($sMask, "(),", "   "));
        $aAux  = explode(" ",$sMask);
        $sAux = strtoupper($aAux[0]);
        switch ($sAux) {
            case 'BOOLEAN':
                if ($vValor == "t") {
                    $vValor = "Sim";
                } elseif ($vValor == "f") {
                    $vValor = "Não";
                }
                break;
            case 'INTEIRO':
                //inteiro(tamanho,comZeros)
                $bComZeros = (bool) $aAux[2];
                $iTam      = (int) $aAux[1];
                if ($bComZeros) {
                    $sTam  = "0".$iTam;
                } else {
                    $sTam  = $iTam;
                }
                $iValor    = (int) $vValor;
                $vValor    = sprintf("%".$sTam."d", $iValor);
                break;
            case 'VALOR':
                //valor(tamanho,decimais)
                $iDec   = (int) $aAux[2];
                $fValor = (float) $vValor;
                $fValor = sprintf("%".$iTam.".".$iDec."f", $fValor);
                $vValor = number_format($fValor, $iDec, ",", ".");
                break;
            case 'MOEDA':
                //moeda(tamanho,decimais)
                $iPre   = (int) $aAux[2];
                $fValor = (float) $vValor;
                $fValor = sprintf("%".$iTam.".".$iPre."f", $fValor);
                $vValor = "R$ ".number_format($fValor, $iPre, ",", ".");
                break;
            case 'PERCENTUAL':
                //percentual(tamanho,decimais)
                $iDec   = (int) $aAux[2];
                $fValor = (float) $vValor;
                $fValor = sprintf("%".$iTam.".".$iDec."f", $fValor);
                $vValor = number_format($fValor, $iDec, ",", ".")." %";
                break;
            case 'DATA':
                //data
                $vValor = dataToBr($vValor);
                break;
            case 'DATAHORA':
                //datahora
                $vValor = dataToBr(substr($vValor,0,10))." ".substr($vValor,11,8);
                break;
            case 'DATAEXTENSO':
                //dataextenso(ComDiaSemana)
                $bComDiaSemana = $aAux[1];
                $vValor = dataExtenso($vValor,$bComDiaSemana);
                break;
            case 'CPF':
                //cpf
                $vValor = numeroToCpf($vValor);
                break;
            case 'CNPJ':
                //cnpj
                $vValor = numeroToCnpj($vValor);
                break;
            case 'CNPJCPF':
                //cnpj
                if (strlen($vValor)==14) {
                    $vValor = numeroToCnpj($vValor);
                } elseif (strlen($vValor)==11) {
                    $vValor = numeroToCpf($vValor);
                }
                break;
            case 'DIGITO':
                //dígito verificador por módulo 11
                if ($vValor > 0) {
                    $vValor = geraDigito($vValor);
                }
                break;
            case 'MASC_PLANO_CONTAS':
                //Busca a máscara a ser utilizada, informando o exercício
                $mascara = pegaConfiguracao("masc_plano_contas",9,$this->aParametros["relExercicio"]);
                //Formata o valor de acordo com a máscara
                $valida = validaMascara($mascara,$vValor);
                if ($valida[0]) {
                    $vValor = $valida[1];
                }
                break;
            case 'MASC_PROG_TRABALHO':
                //Busca a máscara a ser utilizada, informando o exercício
                $mascara = pegaConfiguracao("masc_prog_trabalho",8,$this->aParametros["relExercicio"]);
                //Formata o valor de acordo com a máscara
                $valida = validaMascara($mascara,$vValor);
                if ($valida[0]) {
                    $vValor = $valida[1];
                }
                break;
            case 'MASC_CLASS_DESPESA':
                //Busca a máscara a ser utilizada, informando o exercício
                $mascara = pegaConfiguracao("masc_class_despesa",8,$this->aParametros["relExercicio"]);
                //Formata o valor de acordo com a máscara
                $valida = validaMascara($mascara,$vValor);
                if ($valida[0]) {
                    $vValor = $valida[1];
                }
                break;
            case 'MASC_CLASS_RECEITA':
                //Busca a máscara a ser utilizada, informando o exercício
                $mascara = pegaConfiguracao("masc_class_receita",8,$this->aParametros["relExercicio"]);
                //Formata o valor de acordo com a máscara
                $valida = validaMascara($mascara,$vValor);
                if ($valida[0]) {
                    $vValor = $valida[1];
                }
                break;
            case 'MASC_SETOR':
                //Busca a máscara a ser utilizada, informando o exercício
                $mascara = pegaConfiguracao("mascara_setor");
                //Formata o valor de acordo com a máscara
                $valida = validaMascara($mascara,$vValor);
                if ($valida[0]) {
                    $vValor = $valida[1];
                }
                break;
            case 'MASC_LOCAL':
                //Busca a máscara a ser utilizada, informando o exercício
                $mascara = pegaConfiguracao("mascara_local");
                //Formata o valor de acordo com a máscara
                $valida = validaMascara($mascara,$vValor);
                if ($valida[0]) {
                    $vValor = $valida[1];
                }
                break;
            default:
                $vValor = "Máscara $sMask não existe";
        }

        return $vValor;
    }

    public function campoPDF($nome,$atributos)
    {
        $lar = (int) $atributos["LARGURA"];
        $alt = (int) $atributos["ALTURA"];
        $bor = $atributos["BORDA"];
        $nli = (int) $atributos["NOVALINHA"];
        $ali = $atributos["ALINHAMENTO"];
        $msk = $atributos["MASCARA"];
        $cfu = explode(',',$atributos["CORDEFUNDO"]);
        if ($this->alternar > 0) {
            $cfu = explode(',',$this->corAtual);
        }
        $fun = 1;   //forçar impressão do fundo mesmo que seja branco.
        if ($bor=='') {
            $bor='0';
        }
        if ($ali=='') {
            $ali='L';
        }
        if ($ali=='D') {
            $ali='R';
        }
        if ($ali=='E') {
            $ali='L';
        }
        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome] = $atributos;
            if ($nli>0) {
                $this->detalhe[$this->iContaDet][]["QUEBRALINHA"] = array();
            }
        } else {
            if ($cfu[0]!='') {
                $fun = 1;
                $this->PDF->SetFillColor($cfu[0],$cfu[1],$cfu[2]);
            }
            $vlr = $this->dbPDF[$this->iContaDet]->pegacampo($atributos["NOME"]);
            if (strlen($msk)>2) {
                $vlr = $this->montaMascara($msk,$vlr);
            }
            $this->PDF->cell($lar,$alt,$vlr,$bor,0,$ali,$fun,$lnk);
            if ($cfu[0]!='') {
                $fun = 0;
                $this->PDF->SetFillColor(255,255,255);
            }
        }
    }

    public function campoTextoPDF($nome,$atributos)
    {
        $lar = (int) $atributos["LARGURA"];
        $alt = (int) $atributos["ALTURA"];
        $bor = $atributos["BORDA"];
        $ali = $atributos["ALINHAMENTO"];
        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome] = $atributos;
        } else {
            if ($cfu[0]!='') {
                $fun = 1;
                $this->PDF->SetFillColor($cfu[0],$cfu[1],$cfu[2]);
            }
            $vlr = $this->dbPDF[$this->iContaDet]->pegacampo($atributos["NOME"]);
            $Xant = $this->PDF->GetX();
            $this->aTextoDados["atributos"] = $atributos;
            $this->aTextoDados["texto"]     = $vlr;
            $this->aTextoDados["posicaoX"]  = $Xant;
            $this->bPrintTexto              = true;
            $this->PDF->cell($lar,$alt,$atributos["NOME"],$bor,0,$ali,1);
            if ($cfu[0]!='') {
                $fun = 0;
                $this->PDF->SetFillColor(255,255,255);
            }
        }
    }



    public function parametroPDF($nome,$atributos)
    {
        $lar = (int) $atributos["LARGURA"];
        $alt = (int) $atributos["ALTURA"];
        $bor = $atributos["BORDA"];
        $nli = (int) $atributos["NOVALINHA"];
        $ali = $atributos["ALINHAMENTO"];
        $msk = $atributos["MASCARA"];
        $fun = 1;   //forçar impressão do fundo mesmo que seja branco.
        $cfu = explode(',',$atributos["CORDEFUNDO"]);
        if ($bor=='') {
            $bor='0';
        }
        if ($ali=='') {
            $ali='L';
        }
        if ($ali=='D') {
            $ali='R';
        }
        if ($ali=='E') {
            $ali='L';
        }

        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome] = $atributos;
            if ($nli>0) {
                $this->detalhe[$this->iContaDet][]["QUEBRALINHA"] = array();
            }
        } else {
            if ($cfu[0]!='') {
                $this->PDF->SetFillColor($cfu[0],$cfu[1],$cfu[2]);
            }
            $vlr = $this->aParametros[$atributos["NOME"]];
            if (strlen($msk)>2) {
                $vlr = $this->montaMascara($msk,$vlr);
            }
            $this->PDF->cell($lar,$alt,$vlr,$bor,0,$ali,$fun,$lnk);
            if ($cfu[0]!='') {
                $fun = 0;
                $this->PDF->SetFillColor(255,255,255);
            }
        }
    }

    public function parametroTextoPDF($nome,$atributos)
    {
        $lar = (int) $atributos["LARGURA"];
        $alt = (int) $atributos["ALTURA"];
        $bor = $atributos["BORDA"];
        $ali = $atributos["ALINHAMENTO"];
        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome] = $atributos;
        } else {
            if ($cfu[0]!='') {
                $fun = 1;
                $this->PDF->SetFillColor($cfu[0],$cfu[1],$cfu[2]);
            }
            $vlr = $this->aParametros[$atributos["NOME"]];
            $Xant = $this->PDF->GetX();
            $this->aTextoDados["atributos"] = $atributos;
            $this->aTextoDados["texto"]     = $vlr;
            $this->aTextoDados["posicaoX"]  = $Xant;
            $this->bPrintTexto              = true;
            $this->PDF->cell($lar,$alt,$atributos["NOME"],$bor,0,$ali,1);
            if ($cfu[0]!='') {
                $fun = 0;
                $this->PDF->SetFillColor(255,255,255);
            }
        }
    }



    public function fontePDF($nome,$atributos,$acao)
    {
        if ($acao=='fecha') {
            array_pop($this->fonte);
            end($this->fonte);
            list($key,$aFonte)  = each($this->fonte);
        } else {
            if (is_array($this->fonte)) {
                end($this->fonte);
                list($key,$aFonte)  = each($this->fonte);
            }
            if ($atributos["NOME"]!='') {
                $aFonte["NOME"] = $atributos["NOME"];
            }
            if ($atributos["ALTURA"]>0) {
                $aFonte["ALTURA"] = $atributos["ALTURA"];
            }
            if ($atributos["ESTILO"]!='') {
                $aFonte["ESTILO"] = $atributos["ESTILO"];
            }
            if ($atributos["COR"]!='') {
                $aFonte["COR"] = $atributos["COR"];
            }
            $this->fonte[] = $aFonte;
        }
        $fon = $aFonte["NOME"];
        $alt = (int) $aFonte["ALTURA"];
        $est = strtr($aFonte["ESTILO"],'nisNIS','BIUBIU');
        $cor = explode(',',$aFonte["COR"]);
        if ($this->detalheStatus[$this->iContaDet]=='abre') {
            $this->detalhe[$this->iContaDet][][$nome.'_'.strtoupper($acao)] = $atributos;
        } else {
            if ($cor[0]!='') {
                $this->PDF->SetTextColor($cor[0],$cor[1],$cor[2]);
            }
            $this->PDF->SetFont($fon,$est,$alt);
        }
    }

    public function montaCabecalhoHTML()
    {
        echo '
        <html>
        <head>
        <title></title>
        <script src="ifuncoesJs.js" type="text/javascript"></script>
        <script type="text/javascript">
        function alertaAviso(objeto,tipo,chamada)
        {
            var x = 350;
            var y = 200;
            var sArq = "alerta.inc.php?tipo="+tipo+"&chamada="+chamada+"&obj="+objeto;
            //var wVolta=false;
            mensagem = window.open(sArq,"mensagem","width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y);
        }

    public function MontaCSS()
    {
            var sLinha;
            var sNavegador = navigator.appName;
            if (sNavegador == "Microsoft Internet Explorer") {
                sLinha = "<link rel=STYLESHEET type=text/css href=../stylos_ieLegado.css>";
            } else {
                sLinha = "<link rel=STYLESHEET type=text/css href=../stylos_nsLegado.css>";
            }
            document.write(sLinha);
            }
            MontaCSS();
        </script>
        <meta http-equiv="Pragma content="no-cache">
        <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
        <meta http-eqiv="Expires" content="10 mar 1967 09:00:00 GMT">
        </head>
        <body>
        <table width=100%>
        <tr>
            <td class="labelcenter" height=5 width=100%><font size=1
            color=#535453><b>&raquo; Impressão do Relatório: '.$this->PDF->sTitulo.'</b></font></td>
        </tr>
        </table>';
    }
    public function montaRodapeHTML()
    {
        echo '
        </body>
        </html>';
    }
}
?>
