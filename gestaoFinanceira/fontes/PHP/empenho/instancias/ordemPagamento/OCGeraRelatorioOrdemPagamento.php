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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 18/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCGeraRelatorioOrdemPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.05
                    uc-02.03.22
                    uc-02.03.28
*/

/* include de sistema */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
/* include de regra de negocio */
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAssinatura.class.php";

/**
 * Classe local para gerar o PDF para todos ao mesmo tempo
 *
 * Foi necessário criar essa classe pois para cada ordem de pagamento era necessário gerar um cabeçalho diferente, e passando os dados
 * para a classe normal, gerava o mesmo cabeçalho, sempre com os dados do último
 *
 * @author     Desenvolvedor Henrique Girardi dos Santos
 */

class ListaFormPDFOrdemPagamento extends ListaFormPDF
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
    * Era necessário ajustas a numeração das páginas por ordem de pagamento, iniciando o valor das paginas por nota e o total de paginas também.
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
        if (Sessao::read('inCodAcaoTMP') == 818) {
            $this->Cell(0,5,$stNomAcao.' - Reemissão',1,'RLB','L',1);
        } else {
            $this->Cell(0,5,$stNomAcao,1,'RLB','L',1);
        }

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
        $this->_beginpage($orientation,$format);
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
           $stNomaAcao = preg_replace("/&([a-z])[a-z]+;/i","$1",htmlentities($this->arDadosCabecalho['nom_acao']));//REMOVE OS ACENTOS
           $stNomeArquivo = preg_replace("/[^a-zA-Z0-9]/","", ucwords( $stNomaAcao ) )."_".date("Y-m-d",time())."_".date("His",time()).".pdf";
           $this->OutPut( $stNomeArquivo, 'D' );
        }
    }

    public function PDFOrdemPagamento()
    {
        $rsVazio      = new RecordSet;
        $obRRelatorio = new RRelatorio;

        // Vai ser somente uma pagina

        $arRecordSetTodos = Sessao::read('rsRecordSet');
        $rsListaImpressao = Sessao::read('rsListaImpressao');
        $arFiltro = Sessao::read('filtroRelatorio');
        if (isset($arFiltro['stCtrl']) && $arFiltro['stCtrl']=='imprimirTodos') {
            $dadosImpressao = $rsListaImpressao->getElementos();
        } else {

            $dadosImpressao[0]['cod_ordem'] = $arFiltro['inCodigoOrdem'];
            $dadosImpressao[0]['dt_ordem'] = $arFiltro['stDtOrdem'];
            $dadosImpressao[0]['exercicio'] = $arFiltro['stExercicio'];
            $dadosImpressao[0]['cod_entidade'] = $arFiltro['inCodEntidade'];
            $dadosImpressao[0]['dt_vencimento'] = $arFiltro['dtDataVencimento'];
        }

        foreach ($arRecordSetTodos as $inChave => $rsRecordSet) {
            $arFiltro = $dadosImpressao[$inChave];

            if ($rsRecordSet[4]->getNumLinhas() > 6 || $rsRecordSet[11] > 0) {
                $this->setPaginaFinal(2);
            } else {
                $this->setPaginaFinal(1);
            }

            $stCodOrdem = trim($arFiltro['cod_ordem'].substr(Sessao::getExercicio(),2,2));
            // Adicionar logo no relatorio
            if ( $rsRecordSet[0]->getNumLinhas() ==1 ) {
                $stCodEntidade = $rsRecordSet[0]->getCampo("entidade");
                $inCodEntidade = $stCodEntidade{0};
                $obRRelatorio->setCodigoEntidade( $inCodEntidade );
                $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
            }

            $this->setCodigoBarras('00000000'.str_pad($stCodOrdem, 8,'0',STR_PAD_LEFT).str_pad($arFiltro['cod_entidade'],3,'0',STR_PAD_LEFT).'0');
            $obRRelatorio->setExercicio     ( Sessao::getExercicio() );
            $obRRelatorio->recuperaCabecalho( $arConfiguracao );
            $this->setAcao                 ( "Ordem de Pagamento" );
            $this->setData                 ( SistemaLegado::dataToBr($rsRecordSet['dt_ordem']) );
            $this->setSubTitulo            ( "Ordem N. ".str_pad($arFiltro['cod_ordem'],6,'0',STR_PAD_LEFT) ."/".$arFiltro['exercicio']."                    Vencimento: ".$arFiltro['dt_vencimento']);
            $this->setUsuario              ( Sessao::getUsername() );
            $this->setEnderecoPrefeitura   ( $arConfiguracao );

            // Inicia a recuperação de assinaturas da Autorização na Base
            // Definição de Parâmetros
            $obOPAssinatura = new TEmpenhoOrdemPagamentoAssinatura;
            $obOPAssinatura->setDado("cod_ordem" , $arFiltro['cod_ordem']);
            $obOPAssinatura->setDado("exercicio"       , Sessao::getExercicio());
            $obOPAssinatura->setDado("cod_entidade"    , $arFiltro['cod_entidade']);

            // Novo RecordSet com resultado da consulta

            $rsAssinatura = new RecordSet;
            $obOPAssinatura->recuperaAssinaturasOrdem( $rsAssinatura, "", " ORDER BY num_assinatura ", "" );
            $arAssinaturaSelecionada = array();

            // Popular a sessão com assinaturas selecionadas

            while ($rsAssinatura->each()) {
                $arAssinatura = $rsAssinatura->getObjeto();
                $arAssinaturaSelecionada[] = array	(
                                                    'inId'=>'',
                                                    'inCodEntidade'=>$arAssinatura['cod_entidade'],
                                                    'inCGM'=>$arAssinatura['numcgm'],
                                                    'stNomCGM'=>$arAssinatura['nom_cgm'],
                                                    'stCargo'=>$arAssinatura['cargo'],
                                                    'stCRC'=>'',
                                                    'inPosAssinatura'=>$arAssinatura['num_assinatura']
                                                    );
            }

            // Atualizar a Sessão com as assinaturas recuperadas

            if (count($arAssinaturaSelecionada) > 0) {
                include_once( CAM_FW_PDF."RAssinaturas.class.php" );
                $obRAssinaturas = new RAssinaturas;
                $obRAssinaturas->definePapeisDisponiveis('ordem_pagamento');
                // Método específico
                $obRAssinaturas->montaOrdemPagamento( $arAssinaturaSelecionada );
            }

            //Linha1
            $this->addRecordSet            ( $rsRecordSet[0] );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "L" );
            $this->addCabecalho            ( "ENTIDADE"     , 100, 5, '', '', 'LTR','205,206,205');
            $this->setAlinhamento          ( "L" );
            $this->addCampo                ( "entidade"   , 8, '', '', 'LR','205,206,205');

            //Bloco1
            $this->addRecordSet            ( $rsRecordSet[1] );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "L" );
            $this->addCabecalho            ( "      À TESOURARIA:" , 20, 10, '', '', 'LT');
            $this->addCabecalho            ( ""                    , 60, 10, '', '', 'T');
            $this->addCabecalho            ( ""                    , 20, 10, '', '', 'TR');
            $this->setAlinhamento          ( "L" );
            $this->addCampo                ( "1", 8, '', '', 'L');
            $this->addCampo                ( "2", 8, '', '', '');
            $this->addCampo                ( "3", 8, '', '', 'R');

            //Bloco2
            $this->addRecordSet            ( $rsRecordSet[2] );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "L" );
            $this->addCabecalho            ( "" , 20, 5, '', '', 'L');
            $this->addCabecalho            ( "" , 60, 5, '', '', '');
            $this->addCabecalho            ( "" , 20, 5, '', '', 'R');
            $this->setAlinhamento          ( "R" );
            $this->addCampo                ( "1", 8, '', '', 'L');
            $this->addCampo                ( "2", 8, '', '', '');
            $this->addCampo                ( "3", 8, '', '', 'R');

            //Bloco3
            $this->addRecordSet            ( $rsRecordSet[3] );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "L" );
            $this->addCabecalho            ( "" , 20, 5, '', '', 'L');
            $this->addCabecalho            ( "" , 25, 5, '', '', '');
            $this->addCabecalho            ( "" , 10, 5, '', '', '');
            $this->addCabecalho            ( "" , 25, 5, '', '', '');
            $this->addCabecalho            ( "" , 20, 5, '', '', 'R');
            $this->setAlinhamento          ( "C" );
            $this->addCampo                ( "1", 8, '', '', 'L');
            $this->addCampo                ( "2", 8, '', '', '');
            $this->addCampo                ( "3", 8, '', '', '');
            $this->addCampo                ( "4", 8, '', '', '');
            $this->addCampo                ( "5", 8, '', '', 'R');

            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setAlturaCabecalho      ( 5 );
            $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" , 100, 5, '', '', 'T');
            $this->addCampo                ( "", 8, '', '', '');

            //Bloco4 Liquidações
            $this->addRecordSet            ( $rsRecordSet[4] );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "L" );
            $this->addCabecalho            ( "Empenho"         , 78, 8, '', '', 'LTB','205,206,205');
            $this->setAlinhamento          ( "C" );
            $this->addCabecalho            ( "Valor"           , 11, 8, '', '', 'LTBR','205,206,205');
            $this->addCabecalho            ( "Valor Anulado"   , 11, 8, '', '', 'LTBR','205,206,205');
            $this->setAlinhamento          ( "L" );
            $this->addCampo                ( "1", 8, '', '', 'LRB');
            $this->setAlinhamento          ( "R" );
            $this->addCampo                ( "2", 8, '', '', 'LRB');
            $this->addCampo                ( "3", 8, '', '', 'LRB');

            //Bloco5 - Total Liquidações
            $this->addRecordSet            ( $rsRecordSet[5] );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "L" );
            $this->addCabecalho            ( "" , 78, 5, '', '', 'LT');
            $this->addCabecalho            ( "" , 22, 5, '', '', 'LTR');
            $this->setAlinhamento          ( "R" );
            $this->addCampo                ( "1", 8, 'B', '', 'LBR');
            $this->addCampo                ( "2", 8, 'B', '', 'LBR');

            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setAlturaCabecalho      ( 5 );
            $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" , 100, 5, '', '', '');

            //Bloco Retenções
            if (isset($rsRecordSet[9]) or isset($rsRecordSet['9b'])) {
                // Retenções Orçamentárias
                if ($rsRecordSet[9]) {
                    $this->addRecordSet            ( $rsRecordSet[9] );
                    $this->setQuebraPaginaLista    ( false );
                    $this->setAlturaCabecalho      ( 5 );
                    $this->setAlinhamento          ( "L" );
                    $this->addCabecalho            ( "Retenções Orçamentárias"       , 78, 8, '', '', 'LTBR','220,220,220');
                    $this->setAlinhamento          ( "C" );
                    $this->addCabecalho            ( "Valor Retenções"   , 22, 8, '', '', 'LTBR','220,220,220');
                    $this->setAlinhamento          ( "L" );
                    $this->addCampo                ( "nom_conta", 8, '', '', 'LRB');
                    $this->setAlinhamento          ( "R" );
                    $this->addCampo                ( "vl_retencao", 8, '', '', 'LRB');
                }

              // Retenções Extras
                if ($rsRecordSet['9b']) {
                    $this->addRecordSet            ( $rsRecordSet['9b'] );
                    $this->setQuebraPaginaLista    ( false );
                    $this->setAlturaCabecalho      ( 5 );
                    $this->setAlinhamento          ( "L" );
                    $this->addCabecalho            ( "Retenções Extra-Orçamentárias"       , 78, 8, '', '', 'LTBR','220,220,220');
                    $this->setAlinhamento          ( "C" );
                    $this->addCabecalho            ( $rsRecordSet['9'] ? '' : "Valor Retenção"   , 22, 8, '', '', 'LTBR','220,220,220');
                    $this->setAlinhamento          ( "L" );
                    $this->addCampo                ( "nom_conta", 8, '', '', 'LRB');
                    $this->setAlinhamento          ( "R" );
                    $this->addCampo                ( "vl_retencao", 8, '', '', 'LRB');
                }

                // Tl retenção
                $this->addRecordSet            ( $rsRecordSet[10] );
                $this->setQuebraPaginaLista    ( false );
                $this->setAlturaCabecalho      ( 5 );
                $this->setAlinhamento          ( "L" );
                $this->addCabecalho            ( "" , 78, 5, '', '', 'LT');
                $this->addCabecalho            ( "" , 22, 5, '', '', 'LTR');
                $this->setAlinhamento          ( "R" );
                $this->addCampo                ( "1", 8, 'B', '', 'LBR');
                $this->addCampo                ( "2", 8, 'B', '', 'LBR');

                // Tl Liquido da OP
                $this->addRecordSet            ( $rsRecordSet[11] );
                $this->setQuebraPaginaLista    ( false );
                $this->setAlturaCabecalho      ( 5 );
                $this->setAlinhamento          ( "L" );
                $this->addCabecalho            ( "" , 78, 5, '', '', '');
                $this->addCabecalho            ( "" , 22, 5, '', '', '');
                $this->setAlinhamento          ( "R" );
                $this->addCampo                ( "1", 8, 'B', '', 'LRBT','205,206,205');
                $this->addCampo                ( "2", 8, 'B', '', 'LBRT','205,206,205');

            }

            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setAlturaCabecalho      ( 5 );
            $inRetencoes = 0;
            if (isset($rsRecordSet[9]) or isset($rsRecordSet['9b'])) {
                if(isset($rsRecordSet[9])   ) $inRetencoes = $inRetencoes + $rsRecordSet[9]->getNumLinhas();
                if(isset($rsRecordSet['9b'])) $inRetencoes = $inRetencoes + $rsRecordSet['9b']->getNumLinhas();
                if($inRetencoes > 7)
                     $this->setQuebraPaginaLista    ( true );
                else $this->setQuebraPaginaLista    ( false );
            } else $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" , 100, 5, '', '', '');

            
            if (isset($rsRecordSet['banco_credor'])) {
                //Bloco Bancário
                $this->addRecordSet            ( $rsRecordSet['banco_credor'] );
                $this->setQuebraPaginaLista    ( false );
                $this->setAlturaCabecalho      ( 5 );
                $this->setAlinhamento          ( "C" );
                $this->addCabecalho            ( "" , 35, 8, '', '', 'LT');
                $this->addCabecalho            ( "Dados Bancários" , 35, 8, '', '', 'T');
                $this->addCabecalho            ( "" , 30, 8, '', '', 'TR');
                $this->setAlinhamento          ( "L" );
                $this->addCampo                ( "1", 8, '', '', 'LTRB');
                $this->addCampo                ( "2", 8, '', '', 'LTRB');
                $this->addCampo                ( "3", 8, '', '', 'LTRB');
                
            }
            
            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setAlturaCabecalho      ( 5 );
            $inRetencoes = 0;
            if (isset($rsRecordSet[9]) or isset($rsRecordSet['9b'])) {
                if(isset($rsRecordSet[9])   ) $inRetencoes = $inRetencoes + $rsRecordSet[9]->getNumLinhas();
                if(isset($rsRecordSet['9b'])) $inRetencoes = $inRetencoes + $rsRecordSet['9b']->getNumLinhas();
                if($inRetencoes > 7)
                     $this->setQuebraPaginaLista    ( true );
                else $this->setQuebraPaginaLista    ( false );
            } else $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" , 100, 5, '', '', '');

            //Bloco6
            $this->addRecordSet            ( $rsRecordSet[6] );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "C" );
            $this->addCabecalho            ( "Observações" , 50, 10, '', '', 'LTR');
            $this->addCabecalho            ( "Tesouraria"  , 50, 10, '', '', 'LTR');
            $this->setAlinhamento          ( "L" );
            $this->addCampo                ( "1", 8, '', '', 'LR');
            $this->addCampo                ( "2", 8, '', '', 'LR');

            if ($rsRecordSet[4]->getNumLinhas() > 17) {
                $this->setQuebraPaginaLista(true);
            }
            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setAlturaCabecalho      ( 5 );
            $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" , 50, 5, '', '', 'LBR');
            $this->addCabecalho            ( "" , 50, 5, '', '', 'LBR');

            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setComponenteAgrupado   ( 1 );
            $this->setAlturaCabecalho      ( 5 );
            $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" , 100, 5, '', '', '');

            //Bloco7

            $this->addRecordSet            ( $rsRecordSet[7] );
            $this->setComponenteAgrupado   ( 1 );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "C" );
            $this->addCabecalho            ( "Recibo"  ,  100, 10, '', '', 'LTR');
            $this->setAlinhamento          ( "L" );
            $this->addCampo                ( "1", 8, '', '', 'LR');

            if ($rsRecordSet[4]->getNumLinhas() > 6 && $rsRecordSet[4]->getNumLinhas() < 10) {
                $this->setQuebraPaginaLista(true);
            }

            //Bloco7
            $this->addRecordSet            ( $rsRecordSet[8] );
            $this->setComponenteAgrupado   ( 1 );
            $this->setQuebraPaginaLista    ( false );
            $this->setAlturaCabecalho      ( 5 );
            $this->setAlinhamento          ( "C" );
            $this->addCabecalho            ( ""  ,  80, 10, '', '', 'L');
            $this->addCabecalho            ( ""        ,  20, 10, '', '', 'R');
            $this->setAlinhamento          ( "R" );
            $this->addCampo                ( "1", 8, '', '', 'L');
            $this->setAlinhamento          ( "L" );
            $this->addCampo                ( "9", 8, '', '', 'R');

            //Bloco Vazio
            $this->addRecordSet            ( $rsVazio );
            $this->setComponenteAgrupado   ( 1 );
            $this->setAlturaCabecalho      ( 5 );
            $this->setQuebraPaginaLista    ( false );
            $this->addCabecalho            ( "" ,100, 5, '', '', 'LBR');

            /* Realiza o processamento para montar o PDF de cada uma das nota de liquidações */
            $this->montaPDF();
            $this->InFooter=true;
            $this->Footer();
            $this->InFooter=false;

            /* Zera os valores para poder gerar o próximo pdf, sem que repitam os dados do cabeçalho e quebre a página */
            $this->inIndiceLista = 0;
            $this->arCampo = $this->inAlturaLinha = $this->arQuebraPaginaLista = $this->arRecordSet = $this->arCabecalho = $this->arLarguraColuna =
            $this->arQuebraLinha = $this->arIndentaColuna = array();

            if ($rsRecordSet['arRecibosExtra']) {
                $this->PDFReciboReceita($rsRecordSet['arRecibosExtra']);
            }
        }
    }

    public function PDFReciboReceita($arRecordSetTodos)
    {
        $rsVazio      = new RecordSet;
        $obRRelatorio = new RRelatorio;

        // Vai ser somente uma pagina

        $rsListaImpressao = Sessao::read('rsListaImpressao');
        $arFiltro = Sessao::read('filtroRelatorio');
        if (isset($arFiltro['stCtrl']) && $arFiltro['stCtrl']=='imprimirTodos') {
            $dadosImpressao = $rsListaImpressao->getElementos();
        } else {

            $dadosImpressao[0]['cod_ordem'] = $arFiltro['inCodigoOrdem'];
            $dadosImpressao[0]['dt_ordem'] = $arFiltro['stDtOrdem'];
            $dadosImpressao[0]['exercicio'] = $arFiltro['stExercicio'];
            $dadosImpressao[0]['cod_entidade'] = $arFiltro['inCodEntidade'];
            $dadosImpressao[0]['dt_vencimento'] = $arFiltro['dtDataVencimento'];
        }

        foreach ($arRecordSetTodos as $arRecordSetTMP) {
            $arFiltro = $dadosImpressao[0];

            $this->setPaginaFinal(1);

            $stCodOrdem = trim($arRecordSetTMP['cod_ordem'].substr(Sessao::getExercicio(),2,2));
            // Adicionar logo no relatorio

            $inCodEntidade = $arRecordSetTMP['cod_entidade'];
            $obRRelatorio->setCodigoEntidade( $inCodEntidade );
            $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );

            $this->setCodigoBarras('00000000'.str_pad($stCodOrdem, 8,'0',STR_PAD_LEFT).str_pad($arRecordSetTMP['cod_entidade'],3,'0',STR_PAD_LEFT).'0');
            $obRRelatorio->setExercicio     ( Sessao::getExercicio() );
            $obRRelatorio->recuperaCabecalho( $arConfiguracao );

            $arConfiguracao['nom_modulo']         = 'Recibo de Receita Extra';
            $arConfiguracao['nom_funcionalidade'] = 'Tesouraria';
            $arConfiguracao['nom_acao']           = 'Recibo Reemissão';

            $arDataHora = explode(' ', $arRecordSetTMP['dt_emissao']);
            $this->setAcao              ('Recibo');
            $this->setData              (SistemaLegado::dataToBr($arDataHora[0]));
            $this->stHora =             $arDataHora[1];
            $this->setSubTitulo         ("Recibo de Receita Extra-Orçamentária - Nro. ".str_pad($arRecordSetTMP['cod_recibo_extra'],6,'0',STR_PAD_LEFT) ."/".$arRecordSetTMP['exercicio']);
            $this->setUsuario           (Sessao::getUsername() );
            $this->setEnderecoPrefeitura($arConfiguracao );

            // Inicia a recuperação de assinaturas da Autorização na Base
            // Definição de Parâmetros
            $obOPAssinatura = new TEmpenhoOrdemPagamentoAssinatura;
            $obOPAssinatura->setDado("cod_ordem"   , $arRecordSetTMP['cod_ordem']);
            $obOPAssinatura->setDado("exercicio"   , Sessao::getExercicio());
            $obOPAssinatura->setDado("cod_entidade", $arRecordSetTMP['cod_entidade']);

            // Novo RecordSet com resultado da consulta

            $rsAssinatura = new RecordSet;
            $obOPAssinatura->recuperaAssinaturasOrdem( $rsAssinatura, "", " ORDER BY num_assinatura ", "" );
            $arAssinaturaSelecionada = array();

            // Popular a sessão com assinaturas selecionadas

            while ($rsAssinatura->each()) {
                $arAssinatura = $rsAssinatura->getObjeto();
                $arAssinaturaSelecionada[] = array	(
                                                    'inId'=>'',
                                                    'inCodEntidade'=>$arAssinatura['cod_entidade'],
                                                    'inCGM'=>$arAssinatura['numcgm'],
                                                    'stNomCGM'=>$arAssinatura['nom_cgm'],
                                                    'stCargo'=>$arAssinatura['cargo'],
                                                    'stCRC'=>'',
                                                    'inPosAssinatura'=>$arAssinatura['num_assinatura']
                                                    );
            }

            // Atualizar a Sessão com as assinaturas recuperadas

            if (count($arAssinaturaSelecionada) > 0) {
                include_once( CAM_FW_PDF."RAssinaturas.class.php" );
                $obRAssinaturas = new RAssinaturas;
                $obRAssinaturas->definePapeisDisponiveis('ordem_pagamento');
                // Método específico
                $obRAssinaturas->montaOrdemPagamento( $arAssinaturaSelecionada );
            }

            $stNomPrefeitura = $arRecordSetTMP['cod_entidade'].' - '.$arRecordSetTMP['nom_prefeitura'];
            //Linha1
            $arEntidade = array('0' => array('entidade' => $stNomPrefeitura));

            $rsValorEntidade = new RecordSet;
            $rsValorEntidade->preenche($arEntidade);

            $this->addRecordSet      ($rsValorEntidade);
            $this->setAlturaCabecalho(5);
            $this->addCabecalho      ( '', 100);
            $this->setAlinhamento    ('L');
            $this->addCampo          ('entidade', 8, 'B', '', 'LTRB');

            $stValorExtenso = SistemaLegado::extenso($arRecordSetTMP['valor']);
            $stValorRecibo  = "Valor do Recibo: R$".$arRecordSetTMP['valor']." ( ".$stValorExtenso." )";
            $stCredor       = 'Credor: '.$arRecordSetTMP['cod_credor'].' - '.$arRecordSetTMP['nom_cgm_credor'];
            $stContaReceita = $arRecordSetTMP['cod_estrutural'].'    '.$arRecordSetTMP['cod_plano_despesa'].'    '.$arRecordSetTMP['nom_conta'];
            $arRecordSetValor1 = array( array("2" => $stValorRecibo)
                                      , array("2" => '')
                                      , array("2" => $stCredor)
                                      , array("2" => '')
                                      , array("2" => 'Conta Caixa/Banco:')
                                      , array("2" => '')
                                      , array("2" => 'Conta da Receita:')
                                      , array("2" => $stContaReceita)
                                      , array("2" => "")
            );

            $rsValor1 = new RecordSet;
            $rsValor1->preenche($arRecordSetValor1);

            //Bloco1
            $this->addRecordSet        ($rsValor1);
            $this->setQuebraPaginaLista(false );
            $this->addCabecalho        ('', 100, 8, 'B', '', 'LTRB');
            $this->setAlinhamento      ('L');
            $this->addCampo            ('2', 8,  '', '', 'LR');

            $stRecurso = 'Recurso: ';
            if ($arRecordSetTMP['cod_recurso'] != '') {
                $stRecurso = 'Recurso: '.$arRecordSetTMP['cod_recurso'].' - '.$arRecordSetTMP['nom_recurso'];
            }

            $stComplementoHistorico = 'Referente a Retenção Extra Orçamentária OP '.$arRecordSetTMP['cod_ordem'].'/'.$arRecordSetTMP['exercicio'];
            $arRecordSetValor2 = array( array('3' => 'Histórico')
                                      , array('3' => $arRecordSetTMP['historico'])
                                      , array('3' => $stComplementoHistorico)
                                      , array('3' => $stRecurso)
            );

            $rsValor2 = new RecordSet;
            $rsValor2->preenche($arRecordSetValor2);

            //Bloco2
            $this->addRecordSet        ($rsValor2);
            $this->setQuebraPaginaLista(false);
            $this->addCabecalho        ( '', 100, 8, 'B', '', 'LTRB');
            $this->setAlinhamento      ('L');
            $this->addCampo            ('3', 8,  '', '', 'LR');

            // Titulo do Ultimo Quadro
            $arVazio = array();
            $arVazio[]['nome'] = 'RECIBO';
            $rsVazio = new RecordSet;
            $rsVazio->preenche( $arVazio );
            $rsVazio->setPrimeiroElemento();
            $this->addRecordSet($rsVazio);
            $this->setQuebraPaginaLista( false );
            $this->setAlinhamento ( "C" );
            $this->addCabecalho("", 100, 8, "", '' , 'T');
            $this->setAlinhamento ( "C" );
            $this->addCampo       ("nome", 8, 'B', '', 'TLR' );

            $stData = SistemaLegado::dataExtenso($arDataHora[0]);

            /// dados para o recibo
            $arVazio = array();

            $arVazio[] = array( 'nome'=>'', 'titulo'=>'' );
            $arVazio[] = array( 'nome'=>'', 'titulo'=>'' );
            $arVazio[] = array( 'nome'=>'', 'titulo'=>'Recebi o valor acima informado.' );
            $arVazio[] = array( 'nome'=>'_______________________________________________________', 'titulo'=>'' );

            $stMunicipio = $arRecordSetTMP['nom_municipio'];
            $arVazio[] = array( 'nome'=>"$stMunicipio, $stData", 'titulo'=>'' );

            $rsVazio = new RecordSet;
            $rsVazio->preenche( $arVazio );
            $rsVazio->setPrimeiroElemento();
            $this->addRecordSet($rsVazio);
            $this->setQuebraPaginaLista( false );
            $this->setAlinhamento ( "C" );
            $this->addCabecalho("", 40 , 8, "", '' , 'L');
            $this->addCabecalho("", 60 , 8, "", '' , 'R');
            $this->setAlinhamento ( "R" );
            $this->addCampo       ("titulo", 8, '', '', 'L' );
            $this->setAlinhamento ( "C" );
            $this->addCampo       ("nome",   8, '', '', 'R' );

            ////  Linha vazia
            $arVazio = array();
            $arVazio[]['nome'] = ' ';
            $rsVazio = new RecordSet;
            $rsVazio->preenche( $arVazio );
            $rsVazio->setPrimeiroElemento();
            $this->addRecordSet($rsVazio);
            $this->setQuebraPaginaLista( false );
            $this->setAlinhamento ( "C" );
            $this->addCabecalho("", 100, 8, "", '' , 'T');
            $this->setAlinhamento ( "C" );

            /* Realiza o processamento para montar o PDF de cada uma das nota de liquidações */
            $this->montaPDF();
            $this->InFooter=true;
            $this->Footer();
            $this->InFooter=false;

            /* Zera os valores para poder gerar o próximo pdf, sem que repitam os dados do cabeçalho e quebre a página */
            $this->inIndiceLista = 0;
            $this->arCampo = $this->inAlturaLinha = $this->arQuebraPaginaLista = $this->arRecordSet = $this->arCabecalho = $this->arLarguraColuna =
            $this->arQuebraLinha = $this->arIndentaColuna = array();
        }
        $arConfiguracao['nom_acao'] = 'Ordem de Pagamento';
        $this->setEnderecoPrefeitura   ( $arConfiguracao );
    }

}

$obPDF = new ListaFormPDFOrdemPagamento;
$obPDF->PDFOrdemPagamento();

$obPDF->show();
?>