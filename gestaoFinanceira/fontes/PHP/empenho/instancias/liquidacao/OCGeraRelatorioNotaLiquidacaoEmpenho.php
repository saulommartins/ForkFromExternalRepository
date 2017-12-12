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
    * Página de Formulario
    * Data de Criação   : 20/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCGeraRelatorioNotaLiquidacaoEmpenho.php 65674 2016-06-08 17:18:14Z evandro $

    * Casos de uso: uc-02.03.21
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include CAM_FW_PDF."RRelatorio.class.php";
include CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";

$obRegra = new TEmpenhoEmpenho;
$obRRelatorio = new RRelatorio;
$rsVazio = new RecordSet;

/**
 * Classe local para gerar o PDF para todos ao mesmo tempo
 *
 * Foi necessário criar essa classe pois para cada nota de liquidação era necessário gerar um cabeçalho diferente, e passando os dados
 * para a classe normal, gerava o mesmo cabeçalho, sempre com os dados do último
 *
 * @author     Desenvolvedor Henrique Girardi dos Santos
 */

class ListaFormPDFNotaLiquidacao extends ListaFormPDF
{
    public $inPaginaInicial;
    public $inPaginaFInal;
    public $stSubTituloAtual;

    /**
    * Construtor da classe
    *
    */
    public function _construct()
    {
        parent::ListaFormPDF();
        $this->stSubTituloAtual = "";
    }

    /**
    * Seta a pagina inicial do cabeçalho
    *
    * @param integer $inValor valor da pagina inicial
    * @return void
    */
    public function setPaginaInicial($inValor)
    {
        $this->inPaginaInicial = $inValor;
    }

    /**
    * Seta a pagina final do cabeçalho
    *
    * @param integer $inValor valor da pagina final
    * @return void
    */
    public function setPaginaFinal($inValor)
    {
        $this->inPaginaFinal = $inValor;
    }

    /**
    * Metodo que sobrescreve o metodo da classe ListaFormPDF
    *
    * Ele monta o cabeçalho de acordo com as necessidades encontradas para gerar todos os relatórios ao mesmo tempo.
    * Era necessário ajustas a numeração das páginas por nota de liquidação, iniciando o valor das paginas por nota e o total de paginas também.
    *
    * @return void
    */
    public function Header()
    {
        $this->SetCreator = "URBEM";
        $this->SetFillColor(220);
        $tMargem = $this->tMargin;
        $lMargem = $this->lMargin;
        if ( is_file( CAM_FW_IMAGENS.$this->arDadosCabecalho["logotipo"] ) ) {
            $this->Image( CAM_FW_IMAGENS.$this->arDadosCabecalho["logotipo"]  ,$lMargem,$tMargem,20);
        } elseif ( is_file( $this->arDadosCabecalho["logotipo"] ) ) {
            $this->Image(  $this->arDadosCabecalho["logotipo"] ,$lMargem,$tMargem,20);
        }
        $this->Cell(20,10,'');
        $this->SetFont('Helvetica','B',8);
        $this->SetFillColor(255);
        $X = $this->GetX();
        $Y = $this->GetY();
        $this->Cell(70,4, $this->arDadosCabecalho["nom_prefeitura"]  ,0,'L',1);
        $this->SetFont('Helvetica','',8);
        $this->SetXY($X,$Y+4);
        $this->Cell(70,4,"Fone/Fax: ".$this->arDadosCabecalho["fone"]." / ".$this->arDadosCabecalho["fax"],0,'L',1);
        $this->SetXY($X,$Y+8);
        $this->Cell(70,4,"E-mail: ".$this->arDadosCabecalho["e_mail"] ,0,'L',1);
        $this->SetXY($X,$Y+12);
        $this->Cell(70,4, $this->arDadosCabecalho["logradouro"].",".$this->arDadosCabecalho["numero"]." - ".$this->arDadosCabecalho["nom_municipio"]  ,0,'L',1);

        $this->SetXY($X,$Y+16);
        $this->Cell(70,4,"CEP: ".$this->arDadosCabecalho["cep"],0,'L',1);
        $this->SetXY($X,$Y+20);
        $this->Cell(70,4,"CNPJ: ".$this->arDadosCabecalho['cnpj'],0,'L',1);
        $this->SetFont('Helvetica','B',8);
        $sDisp = $this->DefOrientation;
        $iAjus = 70;
        if ($sDisp=='L') {
            $iAjus = 160;
        }
        $this->SetXY($X+$iAjus,$Y);

        $this->Cell(56,5,$this->arDadosCabecalho['nom_modulo'],1,0,'L',1);
        $this->Cell(0,5,'Versão: '.Sessao::getVersao(),1,0,'L',1);
        $this->SetXY($X+$iAjus,$Y+5);
        $this->Cell(56,5,$this->arDadosCabecalho['nom_funcionalidade'],1,'TRL','L',1);
        $this->Cell(0,5,"Usuário: ".Sessao::getUsername(),1,'RLB','L',1);
        $this->SetXY($X+$iAjus,$Y+10);

        if ($this->stAcao) {
            $this->arDadosCabecalho['nom_acao'] = trim($this->stAcao);
        } else {
            if( $this->stComplementoAcao )
                $stNomAcao = trim($this->arDadosCabecalho['nom_acao']) ." ".$this->stComplementoAcao;
        }
        $stNomAcao = ( $stNomAcao ) ? $stNomAcao : $this->arDadosCabecalho['nom_acao'];
        $this->Cell(0,5,$stNomAcao,1,'RLB','L',1);

        $this->SetFont('Helvetica','',8);
        $this->SetXY($X+$iAjus,$Y+15);
        $this->Cell(0,5,$this->stSubTitulo,1,'RLB','L',1);
        $this->SetXY($X+$iAjus,$Y+20);
        if(!$this->stData)
            $this->insereData();
        $this->Cell(33,5,'Emissão: '.$this->stData,1,0,'L',1);
        $this->Cell(23,5,'Hora: '.$this->stHora,1,0,'L',1);

        if ($this->stSubTitulo != $this->stSubTituloAtual) {
            $this->stSubTituloAtual = $this->stSubTitulo;
            $this->setPaginaInicial(1);
            $this->Cell(0,5,'Página: '.$this->inPaginaInicial.' de '.$this->inPaginaFinal ,1,0,'L',1);
            $this->inPaginaInicial++;
        } else {
            $this->Cell(0,5,'Página: '.$this->inPaginaInicial.' de '.$this->inPaginaFinal ,1,0,'L',1);
            $this->inPaginaInicial++;
        }

        $this->Ln(4);
        $this->Cell(0,1,' ','B',0,'C');
        $this->Ln(3);
    }

    public function AddPage($orientation='', $format='')
    {
        //Start a new page
        if($this->state==0)
            $this->Open();
        $family=$this->FontFamily;
        $style=$this->FontStyle.($this->underline ? 'U' : '');
        $size=$this->FontSizePt;
        $lw=$this->LineWidth;
        $dc=$this->DrawColor;
        $fc=$this->FillColor;
        $tc=$this->TextColor;
        $cf=$this->ColorFlag;
        if ($this->page>0) {
            //Page footer
            $this->InFooter=true;
            //$this->Footer();
            $this->InFooter=false;
            //Close page
            $this->_endpage();
        }
        //Start new page
        $this->_beginpage($orientation,'');
        //Set line cap style to square
        $this->_out('2 J');
        //Set line width
        $this->LineWidth=$lw;
        $this->_out(sprintf('%.2f w',$lw*$this->k));
        //Set font
        if($family)
            $this->SetFont($family,$style,$size);
        //Set colors
        $this->DrawColor=$dc;
        if($dc!='0 G')
            $this->_out($dc);
        $this->FillColor=$fc;
        if($fc!='0 g')
            $this->_out($fc);
        $this->TextColor=$tc;
        $this->ColorFlag=$cf;
        //Page header
        $this->Header();
        //Restore line width
        if ($this->LineWidth!=$lw) {
            $this->LineWidth=$lw;
            $this->_out(sprintf('%.2f w',$lw*$this->k));
        }
        //Restore font
        if($family)
            $this->SetFont($family,$style,$size);
        //Restore colors
        if ($this->DrawColor!=$dc) {
            $this->DrawColor=$dc;
            $this->_out($dc);
        }
        if ($this->FillColor!=$fc) {
            $this->FillColor=$fc;
            $this->_out($fc);
        }
        $this->TextColor=$tc;
        $this->ColorFlag=$cf;
    }

    /**
    * Metodo que sobrescreve o metodo da classe ListaFormPDF
    *
    * Método que gera o arquivo pdf. A diferença entre ele e o da classe é que ele não deve chamar o montaPDF, como na classe mãe
    *
    * @return void
    */
    public function show()
    {
        $arFiltroRelatorio = Sessao::read('filtroRelatorio');
        $this->stFilaImpressao =    $arFiltroRelatorio['stFilaImpressao'];
        $this->inNumeroImpressoes = $arFiltroRelatorio['inNumCopias'];

        if ($this->stFilaImpressao) {
            $stParams = '';
            if (  strtolower($this->DefOrientation) == 'l' ) {
                $stParams .= '-landscape ';
            }
            $stParams .= '-size '.$this->PageFormat;
            $sFile = CAM_FRAMEWORK."tmp/doc_".date("Y-m-d",time()).'_'.date("His",time()).'_'.substr(Sessao::getId(),10,6);
            $sFilePDF = $sFile.".pdf";
            $sFilePS  = $sFile.".ps";
            $this->Output($sFilePDF);
            $cmdo  = " pdf2ps ".$sFilePDF." ".$sFilePS." && ";
            $cmdo .= " lpr -r -P$this->stFilaImpressao ".$sFilePS." -#$this->inNumeroImpressoes";
            exec($cmdo, $aAux);
            exec("rm $sFilePDF", $aAux);
            exec("rm $sFilePS", $aAux);
        } else {
           $stNomaAcao = preg_replace("/&([a-z])[a-z]+;/i","$1",htmlentities($this->arDadosCabecalho['nom_acao'],ENT_NOQUOTES,'UTF-8'));//REMOVE OS ACENTOS
           $stNomeArquivo = preg_replace("/[^a-zA-Z0-9]/","", ucwords( $stNomaAcao ) )."_".date("Y-m-d",time())."_".date("His",time()).".pdf";
           $this->OutPut( $stNomeArquivo, 'D' );
        }
    }

}

$obPDF = new ListaFormPDFNotaLiquidacao();

$arRecordSetTodos = Sessao::read('arRecordSet');
$rsListaImpressao = Sessao::read('rsListaImpressao');
$arFiltroRelatorio = Sessao::read('filtroRelatorio');
if (isset($arFiltroRelatorio['stCtrl']) && $arFiltroRelatorio['stCtrl']=='imprimirTodos') {
    $dadosImpressao = $rsListaImpressao->getElementos();
} else {
    $dadosImpressao[0]['exercicio'] = $arFiltroRelatorio['dtExercicioEmpenho'];
    $dadosImpressao[0]['cod_nota'] = $arFiltroRelatorio['inCodNota'];
    $dadosImpressao[0]['exercicio_nota'] = $arFiltroRelatorio['stExercicioNota'];
    $dadosImpressao[0]['cod_entidade'] = $arFiltroRelatorio['inCodEntidade'];
}

foreach ($arRecordSetTodos as $inChave => $arRecordSet) {
    $arFiltroRelatorio = $dadosImpressao[$inChave];

    // Faz o calculo para saber quantos itens são necessarios para quebrar a pagina, com isso sabe o total de cada um dos relatórios
    $flTotal = ($arRecordSet[5]->getNumLinhas()/20);
    $obPDF->setPaginaFinal((floor($flTotal)+1));

    // Adicionar logo no relatorio
    if ( $arRecordSet[0]->getNumLinhas() == "1" ) {
        $stCodEntidade = $arRecordSet[0]->getCampo("entidade");
        $inCodEntidade = $stCodEntidade{0};
        $obRRelatorio->setCodigoEntidade( $inCodEntidade );
        $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
    }

    $obRRelatorio->setExercicio     (Sessao::getExercicio());
    $obRRelatorio->recuperaCabecalho($arConfiguracao);
    $arFiltro = Sessao::read('filtroRelatorio');

    if ( Sessao::read('acao') == '814' ) {
        $stReemissao = ' Reemissão';
    } else {
        $stReemissao = ' ';
    }

    if ($arFiltroRelatorio['exercicio']) {
        $obPDF->setSubTitulo("Nota N. ".$arFiltroRelatorio['cod_nota']." / ".$arFiltroRelatorio['exercicio_nota'].$stReemissao);
    } else {
        $obPDF->setSubTitulo("Nota N. ".$arFiltroRelatorio['cod_nota']." / ".Sessao::getExercicio().$stReemissao);
    }

    $obPDF->setAcao              ("Nota de Liquidação");
    $obPDF->setUsuario           (Sessao::getUsername());
    $obPDF->setEnderecoPrefeitura($arConfiguracao);

    $obRegra->setDado("cod_nota"     ,$arFiltroRelatorio['cod_nota']);
    $obRegra->setDado("exercicio"    ,$arFiltroRelatorio['exercicio_nota']);
    $obRegra->setDado("cod_entidade" ,$arFiltroRelatorio['cod_entidade']);
    $obRegra->recuperaDadosLiquidacao($rsLiquidacao,'','',$boTransacao);

    $stData = SistemaLegado::dataToBr( $rsLiquidacao->getCampo("dt_liquidacao") );
    $obPDF->setData($stData);

    if (substr($rsLiquidacao->getCampo("hora"),0,8) == "00:00:00") {
        $obPDF->stHora = substr(date("H:m:s"),0,8);
    } else {
        $obPDF->stHora = substr($rsLiquidacao->getCampo("hora"),0,8);
    }

    //Linha1
    $obPDF->addRecordSet        ($arRecordSet[0]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("ENTIDADE", 100, 5, '', '', 'LTR','205,206,205');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("entidade", 8, '', '', 'LR','205,206,205');

    //Linha1
    $obPDF->addRecordSet        ($arRecordSet[1] );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("CREDOR"    , 45, 5, '', '', 'LT');
    $obPDF->addCabecalho        ("CGC/CPF"   , 40, 5, '', '', 'T');
    $obPDF->setAlinhamento      ("C" );
    $obPDF->addCabecalho        ("CGM"       , 15, 5, '', '', 'TR');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("Credor"  , 8, '', '', 'L');
    $obPDF->addCampo            ("CpfCnpj" , 8, '', '', '');
    $obPDF->setAlinhamento      ("C" );
    $obPDF->addCampo            ("Cgm"     , 8, '', '', 'R');

    //Linha2
    $obPDF->addRecordSet        ($arRecordSet[2] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("ENDEREÇO"  , 35, 5, '', '', 'L');
    $obPDF->addCabecalho        ("FONE"      , 20, 5, '', '', '');
    $obPDF->addCabecalho        ("CIDADE"    , 30, 5, '', '', '');
    $obPDF->addCabecalho        ("UF"        , 15, 5, '', '', 'R');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("Endereco", 8, '', '', 'L');
    $obPDF->addCampo            ("Fone"    , 8, '', '', 'B');
    $obPDF->addCampo            ("Cidade"  , 8, '', '', 'B');
    $obPDF->addCampo            ("Uf"      , 8, '', '', 'BR');

    //Linha3
    $obPDF->addRecordSet        ($arRecordSet[3] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("EMPENHO"                     , 25, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("DATA DE EMISSÃO EMPENHO"     , 25, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("DATA DE VENCIMENTO LIQUIDAÇÃO"  , 25, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("DATA DE LIQUIDAÇÃO"          , 25, 5, '', '', 'LTR');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("Empenho"    , 8, '', '', 'LBR');
    $obPDF->addCampo            ("Emissao"    , 8, '', '', 'LBR');
    $obPDF->addCampo            ("Vencimento_Liquidacao" , 8, '', '', 'LBR');
    $obPDF->addCampo            ("Liquidacao" , 8, '', '', 'LBR');

    //Linha4
    $obPDF->addRecordSet        ($arRecordSet[4] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("DESCRIÇÃO"                   , 100, 5, '', '', 'LTR');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("descricao"    , 8, '', '', 'LBR');

    $obPDF->addRecordSet        ($arRecordSet[8] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("OBSERVAÇÃO"   ,  100, 5, '', '', 'LTR','');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("1"       , 8, '', '', 'LR' );

    if ($arRecordSet[21]) {
        //Processo
        $obPDF->addRecordSet        ($arRecordSet[21] );
        $obPDF->setAlturaCabecalho  (5 );
        $obPDF->setQuebraPaginaLista(false );
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCabecalho        ("PROCESSO"   ,  100, 5, '', '', 'LTR','');
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCampo            ("1"       , 8, '', '', 'LR' );    
    }

    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20) {
        $obPDF->addRecordSet        ($arRecordSet[13]);
        $obPDF->setAlturaCabecalho  (5 );
        $obPDF->setQuebraPaginaLista(false );
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCabecalho        ("NÚMERO NOTA FISCAL" , 12.5, 5, '', '', 'LT');
        $obPDF->addCabecalho        ("NÚMERO SÉRIE"       , 12.5, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("DATA EMISSÃO"       , 12.5, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("CÓDIGO VALIDAÇÃO"   , 50  , 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("MODELO"             , 12.5, 5, '', '', 'TR');

        $obPDF->addCampo            ("numero_nota_fiscal" , 8, '', '', 'L');
        $obPDF->addCampo            ("numero_serie"       , 8, '', '', 'LR');
        $obPDF->addCampo            ("data_emissao"       , 8, '', '', 'LR');
        $obPDF->addCampo            ("cod_validacao"      , 8, '', '', 'LR');
        $obPDF->addCampo            ("modelo"             , 8, '', '', 'R');
    }

    //Tipo de Documento - Amazonas
    if ((strtolower(SistemaLegado::pegaConfiguracao( 'seta_tipo_documento_liq_tceam',30, Sessao::getExercicio()))=='true') && $arRecordSet[9]->getCampo('descricao_tipo')<>'') {
         $obPDF->addRecordSet        ($arRecordSet[9] );
         $obPDF->setAlturaCabecalho  (5 );
         $obPDF->setQuebraPaginaLista(false );
         $obPDF->setAlinhamento      ("L" );
         $obPDF->addCabecalho        ("TIPO DE DOCUMENTO" , 70, 5, '', '', 'LT');
         $obPDF->setAlinhamento      ("R" );
         $obPDF->addCabecalho        ("VALOR COMPROMETIDO", 15, 5, '', '', 'T');
         $obPDF->addCabecalho        ("VALOR TOTAL"       , 15, 5, '', '', 'TR');
         $obPDF->setAlinhamento      ("L" );
         $obPDF->addCampo            ("descricao_tipo"    , 8, '', '', 'L');
         $obPDF->setAlinhamento      ("R" );
         $obPDF->addCampo            ("vl_comprometido"   , 8, '', '', '');
         $obPDF->addCampo            ("vl_total"          , 8, '', '', 'R');

         if ($arRecordSet[9]->getCampo('cod_tipo')=='1') { //bilhete
             $obPDF->addRecordSet        ($arRecordSet[10] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );

             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("NÚMERO"         , 30, 5, '', '', 'L');
             $obPDF->addCabecalho        ("DATA DE EMISSÃO", 20, 5, '', '', '');
             $obPDF->addCabecalho        ("DATA DE SAÍDA"  , 20, 5, '', '', '');
             $obPDF->addCabecalho        ("HORA DE SAÍDA"  , 30, 5, '', '', 'R');

             $obPDF->addCampo            ("numero"         , 8, '', '', 'L');
             $obPDF->addCampo            ("dt_emissao"     , 8, '', '', '');
             $obPDF->addCampo            ("dt_saida"       , 8, '', '', '');
             $obPDF->addCampo            ("hora_saida"     , 8, '', '', 'R');

             $obPDF->addRecordSet        ($arRecordSet[11] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("DESTINO"        , 30, 5, '', '', 'L');
             $obPDF->addCabecalho        ("DATA DE CHEGADA", 20, 5, '', '', '');
             $obPDF->addCabecalho        ("HORA DE CHEGADA", 50, 5, '', '', 'R');
             $obPDF->addCampo            ("destino"         , 8, '', '', 'L');
             $obPDF->addCampo            ("dt_chegada"      , 8, '', '', '');
             $obPDF->addCampo            ("hora_chegada"    , 8, '', '', 'R');

             $obPDF->addRecordSet        ($arRecordSet[12] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("MOTIVO"         , 100, 5, '', '', 'LR');
             $obPDF->addCampo            ("motivo"          , 8, '', '', 'LRB');
         } elseif ($arRecordSet[9]->getCampo('cod_tipo')=='2') { //diarias
             $obPDF->addRecordSet        ($arRecordSet[10] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("MATRÍCULA"    , 10, 5, '', '', 'L');
             $obPDF->addCabecalho        ("FUNCIONÁRIO"  , 30, 5, '', '', '');
             $obPDF->addCabecalho        ("DATA DE SAÍDA", 15, 5, '', '', '');
             $obPDF->addCabecalho        ("HORA DE SAÍDA", 15, 5, '', '', '');
             $obPDF->addCabecalho        ("DESTINO"      , 30, 5, '', '', 'R');

             $obPDF->addCampo            ("matricula"   , 8, '', '', 'L');
             $obPDF->addCampo            ("funcionario" , 8, '', '', '');
             $obPDF->addCampo            ("dt_saida"    , 8, '', '', '');
             $obPDF->addCampo            ("hora_saida"  , 8, '', '', '');
             $obPDF->addCampo            ("destino"     , 8, '', '', 'R');

             $obPDF->addRecordSet        ($arRecordSet[11] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );

             $obPDF->addCabecalho        (""                , 40, 5, '', '', 'L');
             $obPDF->addCabecalho        ("DATA DE RETORNO" , 15, 5, '', '', '');
             $obPDF->addCabecalho        ("HORA DE RETORNO" , 15, 5, '', '', '');
             $obPDF->addCabecalho        ("QUANTIDADE"      , 30, 5, '', '', 'R');

             $obPDF->addCampo            ("vazio"       , 8, '', '', 'L');
             $obPDF->addCampo            ("dt_retorno"  , 8, '', '', '');
             $obPDF->addCampo            ("hora_retorno", 8, '', '', '');
             $obPDF->addCampo            ("quantidade"  , 8, '', '', 'R');

             $obPDF->addRecordSet        ($arRecordSet[12] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("MOTIVO"      , 100, 5, '', '', 'LR');
             $obPDF->addCampo            ("motivo"      , 8, '', '', 'LRB');

         } elseif ($arRecordSet[9]->getCampo('cod_tipo')=='3') { //diverso
             $obPDF->addRecordSet        ($arRecordSet[10] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("NÚMERO"           , 10, 5, '', '', 'L');
             $obPDF->addCabecalho        ("DATA"             , 90, 5, '', '', 'R');

             $obPDF->addCampo            ("numero"        , 8, '', '', 'L');
             $obPDF->addCampo            ("data"          , 8, '', '', 'R');

             $obPDF->addRecordSet        ($arRecordSet[11] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("DESCRIÇÃO", 100, 5, '', '', 'LR');
             $obPDF->addCampo            ("descricao", 8, '', '', 'LR');

             $obPDF->addRecordSet        ($arRecordSet[12] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("NOME DO DOCUMENTO", 100, 5, '', '', 'LR');
             $obPDF->addCampo            ("nome_documento", 8, '', '', 'LRB');

         } elseif ($arRecordSet[9]->getCampo('cod_tipo')=='4') { //folha
             $obPDF->addRecordSet        ($arRecordSet[10] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("EXERCÍCIO" , 10, 5, '', '', 'L');
             $obPDF->addCabecalho        ("MÊS"       , 90, 5, '', '', 'R');

             $obPDF->addCampo            ("exercicio" , 8, '', '', 'LB');
             $obPDF->addCampo            ("mes"       , 8, '', '', 'RB');
         } elseif ($arRecordSet[9]->getCampo('cod_tipo')=='5') { //nota
             $obPDF->addRecordSet        ($arRecordSet[10] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("NÚMERO NOTA FISCAL" , 20, 5, '', '', 'L');
             $obPDF->addCabecalho        ("NÚMERO SÉRIE"       , 20, 5, '', '', '');
             $obPDF->addCabecalho        ("NÚMERO SUBSÉRIE"    , 20, 5, '', '', '');
             $obPDF->addCabecalho        ("DATA"               , 40, 5, '', '', 'R');

             $obPDF->addCampo            ("numero_nota_fiscal" , 8, '', '', 'LB');
             $obPDF->addCampo            ("numero_serie"       , 8, '', '', 'B');
             $obPDF->addCampo            ("numero_subserie"    , 8, '', '', 'B');
             $obPDF->addCampo            ("data"               , 8, '', '', 'RB');
         } elseif ($arRecordSet[9]->getCampo('cod_tipo')=='6') { //recibo
             $obPDF->addRecordSet        ($arRecordSet[10] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("TIPO DE RECIBO"   , 100, 5, '', '', 'LR');

             $obPDF->addCampo            ("descricao"        , 8, '', '', 'LR');

             $obPDF->addRecordSet        ($arRecordSet[11] );
             $obPDF->setAlturaCabecalho  (5 );
             $obPDF->setQuebraPaginaLista(false );
             $obPDF->setAlinhamento      ("L" );
             $obPDF->addCabecalho        ("NÚMERO" , 20, 5, '', '', 'L');
             $obPDF->addCabecalho        ("VALOR"  , 20, 5, '', '', '');
             $obPDF->addCabecalho        ("DATA"   , 60, 5, '', '', 'R');

             $obPDF->addCampo            ("numero" , 8, '', '', 'LB');
             $obPDF->addCampo            ("valor"  , 8, '', '', 'B');
             $obPDF->addCampo            ("data"   , 8, '', '', 'RB');
         }
    } else {
        //Vazio
        $obPDF->addRecordSet        ($rsVazio );
        $obPDF->setAlturaCabecalho  (5 );
        $obPDF->setQuebraPaginaLista(false );
        $obPDF->addCabecalho        ("" , 100, 5, '', '', 'T','');
    }   
    
    //Linha ATRIBUTOS
    $obPDF->addRecordSet        ($arRecordSet[7] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        (""               ,  30, 5);
    $obPDF->addCabecalho        (""               ,  70, 5);
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("Nome"            , 7, 'B', '', 1 );
    $obPDF->addCampo            ("Valor"           , 8, '', '' , 1 );

    //Vazio
    $obPDF->addRecordSet        ($rsVazio );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->addCabecalho        ("" , 6, 5);

    //Linha5
    $obPDF->addRecordSet        ($arRecordSet[5] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("ITEM"            , 10, 5, '', '', 'LTBR','205,206,205');
    $obPDF->addCabecalho        ("ESPECIFICAÇÃO"   , 60, 5, '', '', 'LTBR','205,206,205');
    $obPDF->addCabecalho        ("VALOR EMPENHADO" , 15, 5, '', '', 'LTBR','205,206,205');
    $obPDF->addCabecalho        ("VALOR LIQUIDADO" , 15, 5, '', '', 'LTBR','205,206,205');
    $obPDF->setAlinhamento      ("R" );
    $obPDF->addCampo            ("Item"           , 8, '', '', 'LR');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("Especificacao"  , 8, '', '', 'LR');
    $obPDF->setAlinhamento      ("R" );
    $obPDF->addCampo            ("ValorEmpenhado" , 8, '', '', 'LR');
    $obPDF->addCampo            ("ValorLiquidado" , 8, '', '', 'LR');

    //Linha6
    $obPDF->addRecordSet        ($arRecordSet[6] );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCabecalho        ("" , 70, 5, '', '', 'LT');
    $obPDF->setAlinhamento      ("R" );
    $obPDF->addCabecalho        ("" , 15, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("" , 15, 5, '', '', 'LTR','205,206,205');
    $obPDF->setAlinhamento      ("L" );
    $obPDF->addCampo            ("recurso"     , 8, '', '', 'LB');
    $obPDF->setAlinhamento      ("R" );
    $obPDF->addCampo            ("Total"       , 8, '', '', 'LBR');
    $obPDF->addCampo            ("ValorTotal"  , 8, '', '', 'LBR','205,206,205');
    
    $obPDF->addRecordSet        ($rsVazio );
    $obPDF->setAlturaCabecalho  (5 );
    $obPDF->setQuebraPaginaLista(false );
    $obPDF->addCabecalho        ("" , 6, 5);

    //TCEAL
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 02 ) {
        $obPDF->addRecordSet        ($arRecordSet[14] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        
        $obPDF->addCabecalho        ("NÚMERO DO DOCUMENTO" , 20, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DATA"                , 20, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DESCRIÇÃO"           , 60, 5, '', '', 'LTBR','205,206,205');
        
        $obPDF->addCampo            ("nro_documento" , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("dt_documento"  , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("descricao"     , 8, '', '', 'LTBR');
        
        if ( $arRecordSet[14]->getCampo('cod_tipo')=='1' OR $arRecordSet[14]->getCampo('cod_tipo')=='7'){
            $obPDF->addRecordSet        ($arRecordSet[15] );
            $obPDF->setAlturaCabecalho  ( 5 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento      ("L" );
            
            $obPDF->addCabecalho        ("AUTORIZAÇÃO" , 40, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("MODELO"      , 60, 5, '', '', 'LTBR','205,206,205');
            
            $obPDF->addCampo            ("autorizacao"   , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("modelo"        , 8, '', '', 'LTBR');
        }
        
        elseif ( $arRecordSet[14]->getCampo('cod_tipo')=='6' ){
            $obPDF->addRecordSet        ($arRecordSet[15] );
            $obPDF->setAlturaCabecalho  ( 5 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento      ("L" );
            
            $obPDF->addCabecalho        ("AUTORIZAÇÃO" , 20, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("MODELO"      , 20, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("NÚMERO DA CHAVE DE ACESSO"  , 60, 5, '', '', 'LTBR','205,206,205');
            
            $obPDF->addCampo            ("autorizacao"   , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("modelo"        , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("nro_xml_nfe"   , 8, '', '', 'LTBR');
        }
        
        elseif ( $arRecordSet[14]->getCampo('cod_tipo')=='8' ){
            $obPDF->addRecordSet        ($arRecordSet[15] );
            $obPDF->setAlturaCabecalho  ( 5 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento      ("L" );
            
            $obPDF->addCabecalho        ("AUTORIZAÇÃO" , 40, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("MODELO"      , 60, 5, '', '', 'LTBR','205,206,205');
            
            $obPDF->addCampo            ("autorizacao"   , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("modelo"        , 8, '', '', 'LTBR');
        }
        
    }
    //TCETO
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 27 ) {
        $obPDF->addRecordSet        ($arRecordSet[14] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        
        $obPDF->addCabecalho        ("NÚMERO DO DOCUMENTO" , 20, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DATA"                , 20, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DESCRIÇÃO"           , 60, 5, '', '', 'LTBR','205,206,205');
        
        $obPDF->addCampo            ("nro_documento" , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("dt_documento"  , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("descricao"     , 8, '', '', 'LTBR');
        
        if ( $arRecordSet[14]->getCampo('cod_tipo')=='1' OR $arRecordSet[14]->getCampo('cod_tipo')=='7'){
            $obPDF->addRecordSet        ($arRecordSet[15] );
            $obPDF->setAlturaCabecalho  ( 5 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento      ("L" );
            
            $obPDF->addCabecalho        ("AUTORIZAÇÃO" , 40, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("MODELO"      , 60, 5, '', '', 'LTBR','205,206,205');
            
            $obPDF->addCampo            ("autorizacao"   , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("modelo"        , 8, '', '', 'LTBR');
        }
        
        elseif ( $arRecordSet[14]->getCampo('cod_tipo')=='6' ){
            $obPDF->addRecordSet        ($arRecordSet[15] );
            $obPDF->setAlturaCabecalho  ( 5 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento      ("L" );
            
            $obPDF->addCabecalho        ("AUTORIZAÇÃO" , 20, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("MODELO"      , 20, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("NÚMERO DA CHAVE DE ACESSO"  , 60, 5, '', '', 'LTBR','205,206,205');
            
            $obPDF->addCampo            ("autorizacao"   , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("modelo"        , 8, '', '', 'LTBR');
            $obPDF->addCampo            (""   , 8, '', '', 'LTBR');
        }
        
        elseif ( $arRecordSet[14]->getCampo('cod_tipo')=='8' ){
            $obPDF->addRecordSet        ($arRecordSet[15] );
            $obPDF->setAlturaCabecalho  ( 5 );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlinhamento      ("L" );
            
            $obPDF->addCabecalho        ("AUTORIZAÇÃO" , 40, 5, '', '', 'LTBR','205,206,205');
            $obPDF->addCabecalho        ("MODELO"      , 60, 5, '', '', 'LTBR','205,206,205');
            
            $obPDF->addCampo            ("autorizacao"   , 8, '', '', 'LTBR');
            $obPDF->addCampo            ("modelo"        , 8, '', '', 'LTBR');
        }
        
    }
    //TCEPE
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 16 ) {
        $obPDF->addRecordSet        ($arRecordSet[14] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        
        $obPDF->addCabecalho        ("TIPO DE DOCUMENTO"    , 35, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("NÚMERO DO DOCUMENTO"  , 35, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("SÉRIE"                , 20, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("UF"                   , 10, 5, '', '', 'LTBR','205,206,205');
        
        $obPDF->addCampo            ("descricao_tipo"   , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("nro_documento"    , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("serie"            , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("sigla_uf"         , 8, '', '', 'LTBR');        
    }
    
    //TCEMG
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11 && isset($arRecordSet[16]) ) {
        $obPDF->addRecordSet        ($arRecordSet[16] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        
        $obPDF->addCabecalho        ("TIPO DE DOCUMENTO FISCAL"     , 50, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("NÚMERO"           , 25, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("SÉRIE"            , 13, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("DATA"             , 12, 5, '', '', 'LTR');
        
        $obPDF->addCampo            ("tipo_descricao"   , 8, '', '', 'LBR');
        $obPDF->addCampo            ("nro_nota"         , 8, '', '', 'LBR');
        $obPDF->addCampo            ("nro_serie"        , 8, '', '', 'LBR');
        $obPDF->addCampo            ("data_emissao"     , 8, '', '', 'LBR');
        
        $obPDF->addRecordSet        ($arRecordSet[17] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCabecalho        ("CHAVE DE ACESSO".$arRecordSet[17]->getCampo('tipo_chave'), 75, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("NÚMERO DA AIDF"       , 25, 5, '', '', 'LTR');
        
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCampo            ("chave_acesso"         , 8, '', '', 'LBR');
        $obPDF->addCampo            ("aidf"                 , 8, '', '', 'LBR');
        
        $obPDF->addRecordSet        ($arRecordSet[18] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCabecalho        ("INSCRIÇÃO ESTADUAL"   , 50, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("INSCRIÇÃO MUNICIPAL"  , 50, 5, '', '', 'LTR');
        
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCampo            ("inscricao_estadual"   , 8, '', '', 'LBR');
        $obPDF->addCampo            ("inscricao_municipal"  , 8, '', '', 'LBR');
        
        $obPDF->addRecordSet        ($arRecordSet[19] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        $obPDF->addCabecalho        ("VALOR LIQUIDADO"  , 25, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("VALOR ASSOCIADO"  , 25, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("VALOR DESCONTO"   , 25, 5, '', '', 'LTR');
        $obPDF->addCabecalho        ("VALOR LÍQUIDO"    , 25, 5, '', '', 'LTR');
        
        $obPDF->setAlinhamento      ("R" );
        $obPDF->addCampo            ("vl_liquidacao"    , 8, '', '', 'LBR');
        $obPDF->addCampo            ("vl_associado"     , 8, '', '', 'LBR');
        $obPDF->addCampo            ("vl_desconto"      , 8, '', '', 'LBR');
        $obPDF->addCampo            ("vl_total_liquido" , 8, '', '', 'LBR');
    }


    //TCMBA
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 5 ) {        
        $obPDF->addRecordSet        ($arRecordSet[20] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        
        $obPDF->addCabecalho        ("NÚMERO NOTA" , 10, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("SERIE"       , 4 , 5, '', '', 'LTBR','205,206,205');        
        $obPDF->addCabecalho        ("SUB"         , 4 , 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DESCRIÇÃO"   , 52, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DATA"        , 10, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("UF"          , 5 , 5, '', '', 'LTBR','205,206,205');
        $obPDF->setAlinhamento      ("R");
        $obPDF->addCabecalho        ("VALOR"       , 15, 5, '', '', 'LTBR','205,206,205');
        
        $obPDF->setAlinhamento      ("L");
        $obPDF->addCampo            ("nro_nota"     , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("nro_serie"    , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("nro_subserie" , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("descricao"    , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("data_emissao" , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("sigla_uf"     , 8, '', '', 'LTBR');
        $obPDF->setAlinhamento      ("R");
        $obPDF->addCampo            ("vl_nota"      , 8, '', '', 'LTBR');        
    }

    //TCERS - Rio Grande do Sul
    if ( SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 23 ) {
        $obPDF->addRecordSet        ($arRecordSet[14] ); 
        $obPDF->setAlturaCabecalho  ( 5 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlinhamento      ("L" );
        
        $obPDF->addCabecalho        ("NÚMERO DA NOTA FISCAL" , 34, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("SÉRIE"                 , 34, 5, '', '', 'LTBR','205,206,205');
        $obPDF->addCabecalho        ("DATA DE EMISSÃO"       , 32, 5, '', '', 'LTBR','205,206,205');
        
        $obPDF->addCampo            ("nro_nota"     , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("nro_serie"    , 8, '', '', 'LTBR');
        $obPDF->addCampo            ("data_emissao" , 8, '', '', 'LTBR'); 
    }

    $arAssinaturas = Sessao::read('assinaturas');
    
    if (is_array($arAssinaturas) && !array_key_exists('selecionadas', $arAssinaturas)) {
        $arAssinaturas = Sessao::read('assinaturasPdf');
        Sessao::remove('assinaturasPdf');
    }
    
    if (count($arAssinaturas['selecionadas']) > 0) {
        include_once( CAM_FW_PDF."RAssinaturas.class.php" );
        $obRAssinaturas = new RAssinaturas;
        $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
        $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
        $obRAssinaturas->montaPDF( $obPDF );
    }

    /* Realiza o processamento para montar o PDF de cada uma das nota de liquidações */
    $obPDF->montaPDF();
    $obPDF->InFooter=true;
    $obPDF->Footer();
    $obPDF->InFooter=false;

    /* Zera os valores para poder gerar o próximo pdf, sem que repitam os dados do cabeçalho e quebre a página */
    $obPDF->inIndiceLista = 0;
    $obPDF->arCampo = $obPDF->inAlturaLinha = $obPDF->arQuebraPaginaLista = $obPDF->arRecordSet = $obPDF->arCabecalho = $obPDF->arLarguraColuna =
    $obPDF->arQuebraLinha = $obPDF->arIndentaColuna = array();
}

$obPDF->show();

?>
