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
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * $Id: OCGeraRelatorioEmpenhoOrcamentario.php 64072 2015-11-27 12:54:23Z evandro $

    * Casos de uso: uc-02.03.03
                    uc-02.03.17

*/

/* includes de sistema */
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/* includes de regras de negocio */
include CAM_FW_PDF."RRelatorio.class.php";
include CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";

/* includes de mapeamentos */
include CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php";

/* instancias de classes */
$obRegra = new REmpenhoEmpenho;
$obRRelatorio = new RRelatorio;
$rsVazio = new RecordSet;

/**
 * Classe local para gerar o PDF para todos ao mesmo tempo
 *
 * Foi necessário criar essa classe pois para cada empenho orçamentário era necessário gerar um cabeçalho diferente, e passando os dados
 * para a classe normal, gerava o mesmo cabeçalho, sempre com os dados do último
 *
 * @author     Desenvolvedor Henrique Girardi dos Santos
 */

class ListaFormPDFEmpenho extends ListaFormPDF
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
        $stNomAcao = ( isset($stNomAcao) ) ? $stNomAcao : $this->arDadosCabecalho['nom_acao'];
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
        $this->stFilaImpressao    = array_key_exists('stFilaImpressao', $arFiltroRelatorio) ? $arFiltroRelatorio['stFilaImpressao'] : null;
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

}

$obPDF = new ListaFormPDFEmpenho;

$arRecordSetTodos = Sessao::read('rsRecordSet');
$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$rsListaImpressao = Sessao::read('rsListaImpressao');
if (isset($arFiltroRelatorio['stCtrl']) && $arFiltroRelatorio['stCtrl']=='imprimirTodos') {
    $dadosImpressao = $rsListaImpressao->getElementos();
} else {
    $dadosImpressao[0]['exercicio'] = array_key_exists('dtExercicioEmpenho', $arFiltroRelatorio) ? $arFiltroRelatorio['dtExercicioEmpenho'] : null;
    $dadosImpressao[0]['cod_empenho'] = $arFiltroRelatorio['inCodEmpenho'];
    $dadosImpressao[0]['cod_entidade'] = $arFiltroRelatorio['inCodEntidade'];
}
$boTransacao = "";
foreach ($arRecordSetTodos as $inChave => $arRecordSet) {
    $arFiltroRelatorio = $dadosImpressao[$inChave];

    // Faz o calculo para saber quantos itens são necessarios para quebrar a pagina, com isso sabe o total de cada um dos relatórios
    // Precisa ter 1 linha de historio e descricao do empenho, 0 para complementacao da descricao e até 6 itens para que caiba tudo em uma pagina
    $inNumHistorico = $arRecordSet[7]->getNumLinhas();
    $inNumDescEmpenho = $arRecordSet[8]->getNumLinhas();
    $inNumItens = $arRecordSet[9]->getNumLinhas();
    $inNumComplementar = 0;
    if ($obRegra->boComplementar) {
        // cabeçalho e descricao = 2
        $inNumComplementar = 2;
    }
    foreach ($arRecordSet[9]->getElementos() as $key => $value) {        
        $arMax[] = $value['Item'];
    }
    
    //caso existir somento 1 item mostrar apenas 1 pagina final
    $maxItens = max($arMax);    
    if($maxItens != 1)
        $flTotal = ($inNumHistorico+$inNumDescEmpenho+$inNumItens+$inNumComplementar)/$maxItens;
    else
        $flTotal = 1;
    
    if (is_integer($flTotal)) {
        $obPDF->setPaginaFinal($flTotal);
    } else {
        $obPDF->setPaginaFinal((floor($flTotal)+1));
    }

    // Adicionar logo nos relatorios
    if ($arRecordSet[0]->getNumLinhas() == "1") {
        $obRRelatorio->setCodigoEntidade($arRecordSet[0]->getCampo("cod_entidade"));
        $obRRelatorio->setExercicioEntidade(Sessao::getExercicio());
    }

    $obRRelatorio->setExercicio(Sessao::getExercicio());
    $obRRelatorio->recuperaCabecalho($arConfiguracao);
    $obPDF->setModulo("Nota de Empenho");
    $obPDF->setAcao("Nota de Empenho");
    $obPDF->setTitulo("Empenho N. ".$arFiltroRelatorio['cod_empenho']);

    if (array_key_exists('stExercicioEmpenho', $arFiltroRelatorio) ) {
        if ( Sessao::read('reemitir') == 't' ) {
            $obPDF->setSubTitulo("Empenho N. ".$arFiltroRelatorio['cod_empenho']."/".$arFiltroRelatorio['exercicio']. " REEMISSÃO ");
        } else {
            $obPDF->setSubTitulo("Empenho N. ".$arFiltroRelatorio['cod_empenho']."/".$arFiltroRelatorio['exercicio']. " ");
        }
    } else {
        if ( Sessao::read('reemitir') == 't' ) {
            $obPDF->setSubTitulo("Empenho N. ".$arFiltroRelatorio['cod_empenho']."/".Sessao::getExercicio(). " REEMISSÃO ");
        } else {
            $obPDF->setSubTitulo("Empenho N. ".$arFiltroRelatorio['cod_empenho']."/".Sessao::getExercicio(). " ");
        }
    }

    $obPDF->setUsuario(Sessao::getUsername());

    if ($arFiltroRelatorio['exercicio']) {
        $exercicio = $arFiltroRelatorio['exercicio'];
    } else {
        $exercicio = Sessao::getExercicio();
    }
    $obRegra->setExercicio($exercicio);
    $obRegra->setCodEmpenho($arFiltroRelatorio['cod_empenho']);
    $obRegra->obROrcamentoEntidade->setCodigoEntidade($arFiltroRelatorio['cod_entidade']);
    $obRegra->consultar($boTransacao);

    // Inicia a recuperação de assinaturas da Autorização na Base
    // Definição de Parâmetros

    $obEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
    $obEmpenhoAssinatura->setDado("cod_empenho" , $obRegra->getCodEmpenho());
    $obEmpenhoAssinatura->setDado("exercicio"   , $obRegra->getExercicio());
    $obEmpenhoAssinatura->setDado("cod_entidade", $obRegra->obROrcamentoEntidade->getCodigoEntidade());

    // Novo RecordSet com resultado da consulta

    $rsAssinatura = new RecordSet;
    $obEmpenhoAssinatura->recuperaAssinaturasEmpenho( $rsAssinatura, "", " ORDER BY num_assinatura ", "" );
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
        $obRAssinaturas->definePapeisDisponiveis('nota_empenho');
        // Método específico
        $obRAssinaturas->montaNotaEmpenho($arAssinaturaSelecionada);
    }

    $obPDF->setData($obRegra->getDtEmpenho());
    if (substr($obRegra->getHora(),0,8) == "00:00:00") {
        $obPDF->stHora = substr(date("H:m:s"),0,8);
    } else {
        $obPDF->stHora = substr($obRegra->getHora(),0,8);
    }
    $obPDF->setEnderecoPrefeitura($arConfiguracao);

    //Linha1
    $obPDF->addRecordSet        ($arRecordSet[0]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("ENTIDADE", 100, 5, '', '', 'LTR','205,206,205');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("entidade", 8, '', '', 'LR','205,206,205');

    //Linha1
    $obPDF->addRecordSet        ($arRecordSet[1]);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("ÓRGÃO", 47, 5, '', '', 'LT');
    $obPDF->addCabecalho        ("UNIDADE", 38, 5, '', '', 'RT');
    $obPDF->addCabecalho        ("TIPO", 15, 5, '', '', 'LTR');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("Orgao", 8, '', '', 'L');
    $obPDF->addCampo            ("Unidade", 8, '', '', 'R');
    $obPDF->addCampo            ("Tipo", 8, '', '', 'LR');

    //Linha2
    $obPDF->addRecordSet        ($arRecordSet[2]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("DOTAÇÃO", 85, 5, '', '', 'L');
    $obPDF->addCabecalho        ("", 15, 5, '', '', 'LR');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("Dotacao", 8, '', '', 'LDR');
    $obPDF->addCampo            ("", 8, '', '', 'LDR');

    //Linha3
    $obPDF->addRecordSet        ($arRecordSet[3]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("CREDOR", 45, 5, '', '', 'LT');
    $obPDF->addCabecalho        ("CGC/CPF", 40, 5, '', '', 'T');
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCabecalho        ("CGM", 15, 5, '', '', 'TR');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("Credor", 8, '', '', 'L');
    $obPDF->addCampo            ("CpfCnpj", 8, '', '', '');
    $obPDF->setAlinhamento      ("C");
    $obPDF->addCampo            ("Cgm", 8, '', '', 'R');

    //Linha4
    $obPDF->addRecordSet        ($arRecordSet[4]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("ENDEREÇO", 35, 5, '', '', 'L');
    $obPDF->addCabecalho        ("FONE", 20, 5, '', '', '');
    $obPDF->addCabecalho        ("CIDADE", 30, 5, '', '', '');
    $obPDF->addCabecalho        ("UF", 15, 5, '', '', 'R');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("Endereco", 8, '', '', 'L');
    $obPDF->addCampo            ("Fone", 8, '', '', 'B');
    $obPDF->addCampo            ("Cidade", 8, '', '', 'B');
    $obPDF->addCampo            ("Uf", 8, '', '', 'BR');

    //Linha5
    $obPDF->addRecordSet        ($arRecordSet[5]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("AUTORIZAÇÃO", 33, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("DATA DE EMISSÃO", 33, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("DATA DE VENCIMENTO", 34, 5, '', '', 'LTR');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("Autorizacao", 8, '', '', 'LBR');
    $obPDF->addCampo            ("Emissao", 8, '', '', 'LBR');
    $obPDF->addCampo            ("Vencimento", 8, '', '', 'LBR');

    //Linha6
    $obPDF->addRecordSet        ($arRecordSet[6]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("R");
    $obPDF->addCabecalho        ("VALOR ORÇADO", 25, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("SALDO ANTERIOR", 25, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("VALOR DO EMPENHO", 25, 5, '', '', 'LTR');
    $obPDF->addCabecalho        ("SALDO ATUAL", 25, 5, '', '', 'LTR');
    $obPDF->setAlinhamento      ("R");
    $obPDF->addCampo            ("ValorOrcado", 8, '', '', 'LBR');
    $obPDF->addCampo            ("SaldoAnterior", 8, '', '', 'LBR');
    $obPDF->addCampo            ("ValorEmpenho", 8, '', '', 'LBR');
    $obPDF->addCampo            ("SaldoAtual", 8, '', '', 'LBR');

    $obPDF->addRecordSet        ($arRecordSet[7]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("HISTÓRICO",  100, 5, 'B', '', 'LTR','');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("Historico", 8, '', '', 'LRB' );

    $obPDF->addRecordSet        ($arRecordSet[8]);
    $obPDF->setAlturaCabecalho  (5);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCabecalho        ("DESCRIÇÃO DO EMPENHO",  100, 5, 'B', '', 'LTR','');
    $obPDF->setAlinhamento      ("L");
    $obPDF->addCampo            ("1", 8, '', '', 'LRB' );

    if ($obRegra->boComplementar) {
        $arComplementar = array(0 => array( 1 => "Este empenho é complementar ao Empenho ".$obRegra->inCodEmpenhoOriginal."/".$obRegra->stExercicioEmpenhoOriginal));

        $rsComplementar = new RecordSet;
        $rsComplementar->preenche($arComplementar);
        $obPDF->addRecordSet        ($rsComplementar);
        $obPDF->setAlturaCabecalho  (5);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento      ("L");
        $obPDF->addCabecalho        ("EMPENHO COMPLEMENTAR",  100, 5, 'B', '', 'LTR','');
        $obPDF->setAlinhamento      ("L");
        $obPDF->addCampo            ("1", 8, '', '', 'LRB');

    }

    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20 && !$obRegra->boComplementar) {
        include_once( CAM_GPC_TCERN_MAPEAMENTO . "TTCERNFundeb.class.php" );
        $stFiltroTCERN = " WHERE cod_empenho = ".$obRegra->getCodEmpenho()."
                             AND cod_entidade = ".$obRegra->obROrcamentoEntidade->getCodigoEntidade()."
                             AND exercicio = '".$obRegra->getExercicio()."' ";
        $obTTCERNFundeb = new TTCERNFundeb;
        $obTTCERNFundeb->recuperaRelacionamento($rsFundeb, $stFiltroTCERN);

        include_once( CAM_GPC_TCERN_MAPEAMENTO . "TTCERNRoyalties.class.php" );
        $obTTCERNRoyalties = new TTCERNRoyalties;
        $obTTCERNRoyalties->recuperaRelacionamento($rsRoyalties, $stFiltroTCERN);

        $rsTCERN = new RecordSet;
        $rsTCERN->preenche(
            array(
                array(
                    'fundeb' => $rsFundeb->getCampo('codigo'),
                    'royalties' => $rsRoyalties->getCampo('codigo')
                )
            )
        );

        $obPDF->addRecordSet        ($rsTCERN);
        $obPDF->setAlturaCabecalho  (5);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento      ("L");
        $obPDF->addCabecalho        ("FUNDEB",  50, 5, 'B', '', 'LTR','');
        $obPDF->addCabecalho        ("ROYALTIES",  50, 5, 'B', '', 'LTR','');
        $obPDF->setAlinhamento      ("L");
        $obPDF->addCampo            ("fundeb", 8, '', '', 'LRB' );
        $obPDF->addCampo            ("royalties", 8, '', '', 'LRB' );
    }

    //Linha9
    $obPDF->addRecordSet         ($arRecordSet[11]);
    $obPDF->setAlturaCabecalho   (5);
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlinhamento       ("L");
    $obPDF->addCabecalho         ("", 30, 5);
    $obPDF->addCabecalho         ("", 70, 5);
    $obPDF->setAlinhamento       ("L");
    $obPDF->addCampo             ("Nome", 7, 'B', '', 1 );
    $obPDF->addCampo             ("Valor", 8, '', '' , 1 );

    $obPDF->addRecordSet         ($rsVazio);
    $obPDF->setAlturaCabecalho   (5);
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->addCabecalho         ("", 6, 5);

    //Linha7
    $obPDF->addRecordSet         ($arRecordSet[9]);
    $obPDF->setAlturaCabecalho   (5);
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCabecalho         ("ITEM", 5, 5, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCabecalho         ("QUANTIDADE", 8, 5, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento       ("L");
    $obPDF->addCabecalho         ("UNIDADE", 7, 5, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho         ("ESPECIFICAÇÃO",50, 5, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCabecalho         ("VALOR UNITÁRIO", 15, 5, 'B', '', 'LTRB','205,206,205');
    $obPDF->addCabecalho         ("VALOR TOTAL", 15, 5, 'B', '', 'LTRB','205,206,205');
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCampo             ("Item", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCampo             ("Quantidade", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento       ("L");
    $obPDF->addCampo             ("simbolo", 8, '', '', 'LTRB' );
    $obPDF->addCampo             ("Especificacao", 8, '', '', 'LTRB' );
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCampo             ("ValorUnitario", 8, '', '', 'LTRB' );
    $obPDF->addCampo             ("ValorTotal", 8, '', '', 'LTRB' );

    //Linha8
    $obPDF->addRecordSet         ($arRecordSet[10]);
    $obPDF->setAlturaCabecalho   (5);
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlinhamento       ("L");
    $obPDF->addCabecalho         ("", 85, 5, '', '', 'LR');
    $obPDF->addCabecalho         ("", 15, 5, '', '', 'LR','205,206,205');
    $obPDF->addCampo             ("[PAO]", 8, '', '', 'LR');
    $obPDF->setAlinhamento       ("R");
    $obPDF->addCampo             ("[Total]", 8, '', '', 'LR','205,206,205');

    $obPDF->addRecordSet         ($rsVazio);
    $obPDF->setComponenteAgrupado(1);
    $obPDF->setAlturaCabecalho   (5);
    $obPDF->setQuebraPaginaLista (false );
    $obPDF->setAlinhamento       ("C" );
    $obPDF->addCabecalho         ("" , 100, 8, '', '', 'T');

    $obPDF->addRecordSet         ($rsVazio );
    $obPDF->setComponenteAgrupado(1 );
    $obPDF->setAlturaCabecalho   (5 );
    $obPDF->setQuebraPaginaLista (false );
    $obPDF->setAlinhamento       ("C" );
    $obPDF->addCabecalho         ("Assinaturas" , 100, 8, '', '', 'LTRB');
    $obPDF->addCampo             (" "  , 8, '', '', 'LR');

    $obPDF->addRecordSet         ($rsVazio );
    $obPDF->setComponenteAgrupado(1 );
    $obPDF->setAlturaCabecalho   (5 );
    $obPDF->setQuebraPaginaLista (false );
    $obPDF->setAlinhamento       ("C" );
    $obPDF->addCabecalho         ("" , 100, 8, '', '', 'LR');
    $obPDF->addCampo             (" "  , 8, '', '', 'LR');

    $obPDF->addRecordSet         ($arRecordSet[12] );
    $obPDF->setComponenteAgrupado(1 );
    $obPDF->setAlturaCabecalho   (5 );
    $obPDF->setQuebraPaginaLista (false );
    $obPDF->setAlinhamento       ("C" );
    $obPDF->addCabecalho         (""                   ,  2, 8,  '', '', 'LR' );
    $obPDF->addCabecalho         ("AUTORIZO A DESPESA" , 32, 9, 'B', '', 'LTR');
    $obPDF->addCabecalho         ("CONTADORIA"         , 32, 9, 'B', '', 'LTR');
    $obPDF->addCabecalho         ("PAGUE-SE"           , 32, 9, 'B', '', 'LTR');
    $obPDF->addCabecalho         (""                   ,  2, 8,  '', '', 'LR' );
    $obPDF->addCampo             (""           , 8, '', '', 'LR');
    $obPDF->addCampo             ("autorizo"   , 8, '', '', 'LR');
    $obPDF->addCampo             ("contadoria" , 8, '', '', 'LR');
    $obPDF->addCampo             ("pague"      , 8, '', '', 'LR');
    $obPDF->addCampo             (""           , 8, '', '', 'LR');

    $obPDF->addRecordSet         ($rsVazio );
    $obPDF->setComponenteAgrupado(1 );
    $obPDF->setAlturaCabecalho   (5 );
    $obPDF->setQuebraPaginaLista (false);
    $obPDF->setAlinhamento       ("C");
    $obPDF->addCabecalho         ("", 2, 8, '', '', 'LB');
    $obPDF->addCabecalho         ("", 32, 8, '', '', 'TB');
    $obPDF->addCabecalho         ("", 32, 8, '', '', 'TB');
    $obPDF->addCabecalho         ("", 32, 8, '', '', 'TB');
    $obPDF->addCabecalho         ("", 2, 8, '', '', 'RB');
    $obPDF->addCampo             ("", 8,'', '', 'T');

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
