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
      * Página de Relatório.
      * Data de Criação: 30/09/2008

      * @author Desenvolvedor: Diogo Zarpelon

      * @ignore

      $Id:$

      **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenho.class.php" );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioAutorizacao.class.php"         );

$obRRelatorio    = new RRelatorio;
$obRegra         = new REmpenhoRelatorioAutorizacao;

//seta elementos do filtro
$stFiltro = "";

$arFiltro = Sessao::read('filtroRelatorio');

$arAutorizacoesHomologacao = Sessao::read('stImpressaoAutorizacao');

class HeaderPersonalizado extends ListaFormPDF
{
    public $arDatas;
    public $inCountData = 0;

    public function Header()
    {
        if ($this->boQuebraForcada) {
            list($ano, $mes, $dia) = explode("-",$this->arDatas[($this->inCountData - 1)]);
        } else {
            list($ano, $mes, $dia) = explode("-",$this->arDatas[$this->inCountData++]);
        }

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
        $this->Cell(33,5,'Emissão: '.$dia."/".$mes."/".$ano,1,0,'L',1);
        $this->Cell(23,5,'Hora: '.$this->stHora,1,0,'L',1);
        $this->AliasNbPages();

        if ($this->boQuebraContagemPorSubTitulo) {
            if ($this->inPaginaInicial == null) {
                if ( !$this->stSubTituloAtual or ( $this->stSubTituloAtual != $this->stSubTitulo  ) ) {
                    $this->stSubTituloAtual = $this->stSubTitulo;
                    $this->pageSubTitulo = 1;
                } else {
                    $this->pageSubTitulo++;
                }
                $this->Cell(0,5,'Página: '.$this->pageSubTitulo.' de '.$this->arTotalPaginasSubTitulo[$this->stSubTitulo] ,1,0,'L',1);

            } else {
                $this->Cell(0,5,'Página: '.( $this->PageNo() + $this->inPaginaInicial ) ,1,0,'L',1);
            }
        } else {
            if ($this->inPaginaInicial == null) {
                $this->Cell(0,5,'Página: '.$this->PageNo().' de '.$this->AliasNbPages ,1,0,'L',1);
            } else {
                $this->Cell(0,5,'Página: '.( $this->PageNo() + $this->inPaginaInicial ) ,1,0,'L',1);
            }
        }
        $this->Ln(4);
        $this->Cell(0,1,' ','B',0,'C');
        $this->Ln(3);
    }
}

$obRRelatorio = new RRelatorio;
$obPDF        = new HeaderPersonalizado;
$obPDF->setCampoSubTitulo       ( 'subtitulo' );
$obPDF->setQuebraContagemPorSubTitulo ( true );
$inCount = 0;

$arDt = array();

while ( $inCount <  count($arAutorizacoesHomologacao) ) {
    $stData = ""; $arConfiguracao = array();
    $obRegra  = new REmpenhoRelatorioAutorizacao;
    $rsVazio      = new RecordSet;
    $stFiltro = "";

    $arFiltro = array();

    $arFiltro['inCodEntidade'] = $arAutorizacoesHomologacao[ $inCount ] [ 'inCodEntidade' ] ;
    $arFiltro['inCodAutorizacao']  = $arAutorizacoesHomologacao[ $inCount ] [ 'inCodAutorizacao' ] ;
    $arFiltro['inCodPreEmpenho'] = $arAutorizacoesHomologacao[ $inCount ] [ 'inCodPreEmpenho' ] ;
    $arFiltro['inCodDespesa'] = $arAutorizacoesHomologacao[ $inCount ] [ 'inCodDespesa' ] ;

    if ($arFiltro['inCodEntidade'] != "") {
        $stFiltro .= " AND ae.cod_entidade = " . $arFiltro['inCodEntidade'];
    }
    if ($arFiltro['inCodAutorizacao'] != "") {
        $stFiltro .= " AND ae.cod_autorizacao = " . $arFiltro['inCodAutorizacao'];
    }
    if ($arFiltro['inCodPreEmpenho'] != "") {
        $stFiltro .= " AND ae.cod_pre_empenho = " . $arFiltro['inCodPreEmpenho'];
    }

    $obRegra->setDotacao($arFiltro['inCodDespesa']);

    if ( $arFiltro['stExercicio'] != "" )
    $stFiltro .= " AND ae.exercicio = '" . $arFiltro['stExercicio'] . "' ";
    else
    $stFiltro .= " AND ae.exercicio = '" . Sessao::getExercicio() . "' ";

    if ($arFiltro['stAcao'] == 'imprimirAnulacao' or $arFiltro['stAcao'] == 'reemitir') {
        Sessao::write('tipoRelatorio', 'anulacao');
        $obRegra->geraRecordSet( $arRecordSet,"cod_pre_empenho, num_item",'anulacao', $stFiltro);
    } else {
        Sessao::write('tipoRelatorio', 'autorizacao');
        $obRegra->geraRecordSet( $arRecordSet , "", 'autorizacao', $stFiltro);
    }

    if ($arFiltro['stExercicio']) {
        $arRecordSet[0]->setCampo( 'subtitulo' ,  "Autorização de Empenho N. " . $arFiltro['inCodAutorizacao'] . "/".$arFiltro['stExercicio'] , true );
    } else {
        $arRecordSet[0]->setCampo( 'subtitulo' ,  "Autorização de Empenho N. " . $arFiltro['inCodAutorizacao'] . "/".Sessao::getExercicio() ,  true );
    }

    $rsRecordSet = $arRecordSet;

    $obMap        = new TEmpenhoAutorizacaoEmpenho;

    $obMap->setDado("cod_autorizacao" , $arFiltro['inCodAutorizacao'] );
    if( $arFiltro['exercicio'] )
    $obMap->setDado("exercicio"       , $arFiltro['exercicio']  );
    else
    $obMap->setDado("exercicio"       , Sessao::getExercicio()            );
    $obMap->setDado("cod_entidade"    , $arFiltro['inCodEntidade']    );

    $stTitulo = "Nota de Autorização de Empenho";
    $obMap->recuperaDadosAutorizacao($rsAutorizacao,'','',$boTransacao);

    $stData = SistemaLegado::dataToBr( $rsAutorizacao->getCampo("dt_autorizacao") );
    $arDt[] = $rsAutorizacao->getCampo("dt_autorizacao");

    // Adicionar logo nos relatorios
    if ( $rsRecordSet[10]->getNumLinhas() == "1" ) {
        $stCodEntidade = $rsRecordSet[10]->getCampo("entidade");
        $inCodEntidade = $stCodEntidade{0};
        $obRRelatorio->setCodigoEntidade( $inCodEntidade );
        $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
    }

    $obRRelatorio->setExercicio     ( Sessao::getExercicio() );
    $obRRelatorio->recuperaCabecalho( $arConfiguracao );

    $obPDF->setAcao                 ( $stTitulo );

    $obPDF->setUsuario              ( Sessao::getUsername() );
    $obPDF->setEnderecoPrefeitura   ( $arConfiguracao );
    $obPDF->setData                 ( $stData );

    if (substr($rsAutorizacao->getCampo("hora"),0,8) == "00:00:00") {
        $obPDF->stHora              = substr(date("H:m:s"),0,8);
    } else {
        $obPDF->stHora              = substr($rsAutorizacao->getCampo("hora"),0,8);
    }

    $obPDF->addRecordSet( $rsRecordSet[0] );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "Fornecedor"  ,50, 6,  'B','', 'LT');
    $obPDF->addCabecalho   ( "CNPJ/CPF"    ,40, 6, 'B','', 'T');
    $obPDF->addCabecalho   ( "CGM"         ,10, 6, 'B','', 'RT');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "fornecedor" , 8 ,'','','L');
    $obPDF->addCampo       ( "cpf_cnpj"   , 8 ,'','','0');
    $obPDF->addCampo       ( "numcgm"     , 8 ,'','','R');

    $obPDF->addRecordSet( $rsRecordSet[1] );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "Endereço" ,50, 6, 'B','','L');
    $obPDF->addCabecalho   ( "Fone"     ,15, 6, 'B','','0');
    $obPDF->addCabecalho   ( "Cidade"   ,30, 6, 'B','','0');
    $obPDF->addCabecalho   ( "UF"       ,5 , 6, 'B','','R');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "endereco"   , 8 , '','','LB');
    $obPDF->addCampo       ( "telefone"   , 8 , '','','B');
    $obPDF->addCampo       ( "cidade"     , 8 , '','','B');
    $obPDF->addCampo       ( "uf"         , 8 , '','','RB');

    $obPDF->addRecordSet( $rsRecordSet[10] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "Entidade"  ,100, 6, 'B', '', 'RL');
    $obPDF->addCampo       ( "entidade"  , 8 ,'','','RL');

    $obPDF->addRecordSet( $rsRecordSet[2] );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "Orgão"       ,50, 6, 'B', '', 'L');
    $obPDF->addCabecalho   ( "Unidade"     ,50, 6, 'B', '', 'R');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "orgao"     , 8 ,'','','L');
    $obPDF->addCampo       ( "unidade"   , 8 ,'','','R');
       
    $obPDF->addRecordSet( $rsRecordSet[3] );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "Dotação" ,100, 6 ,'B','','LR');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "dotacao"     , 8 ,'','','LR');

    $rsRecordSetPAO = new RecordSet();
    $rsRecordSetPAO->preenche($rsRecordSet[3]->getElementos());

    $obPDF->addRecordSet( $rsRecordSetPAO );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "PAO" ,100, 6 ,'B','','LR');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "pao"     , 8 ,'','','LR');

    $rsRecordSetRecurso = new RecordSet();
    $rsRecordSetRecurso->preenche($rsRecordSet[3]->getElementos());

    $obPDF->addRecordSet( $rsRecordSetRecurso );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "RECURSO" ,100, 6 ,'B','','LR');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "recurso"     , 8 ,'','','LBR');

    $obPDF->addRecordSet( $rsRecordSet[4] );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->addCabecalho   ( "DATA DE VALIDADE"   ,100, 6, 'B', '', 'RL');
    $obPDF->addCampo       ( "dt_validade_final"  , 8 ,'','','RL');

    // Descricao
    $obPDF->addRecordSet            ( $rsRecordSet[5] );
    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setQuebraPaginaLista    ( false );
    $obPDF->setAlinhamento          ( "L" );
    $obPDF->addCabecalho            ( "DESCRIÇÃO RESUMIDA DO EMPENHO"   ,  100, 5, 'B', '', 'LTR','');
    $obPDF->setAlinhamento          ( "L" );
    $obPDF->addCampo                ( "descricao"       , 8, '', '', 'LRB' );

    // Informações da compra direta e licitação sobre as observações/justificativa da solicitacao de compras.
    $inContAux = 0;
    $observacao = Sessao::read('observacaoSolicitacao');

    if ($observacao !='') {
        $arObsJust = explode('§§',$observacao);
        if ( count($arObsJust)>0 ) {

            foreach ($arObsJust as $chaveArrayObsJust => $dadosObsJust) {
                $stObservacaoJustificativa = str_replace( chr(10) , "", $dadosObsJust );
                $stObservacaoJustificativa = wordwrap( $stObservacaoJustificativa , 123, chr(13) );
                $arObservacaoJustificativaLinhasSeparadas = explode( chr(13), $stObservacaoJustificativa );

                foreach ($arObservacaoJustificativaLinhasSeparadas as $chaveArJustObs => $decrObsJustLinhas) {
                    $arObsJustifLinhasSeparadas[] = $decrObsJustLinhas;
                }
            }

            foreach ($arObsJustifLinhasSeparadas as $stDescricao) {
                $arDescricaoObservacao[$inContAux]['observacaoSolicitacao'] = $stDescricao;
                $inContAux++;
            }
        }
        $rsRecordSetObservacao = new RecordSet;
        $rsRecordSetObservacao->preenche( $arDescricaoObservacao );

        // Observacao / justificativa
        $obPDF->addRecordSet            ( $rsRecordSetObservacao );
        $obPDF->setAlturaCabecalho      ( 5 );
        $obPDF->setQuebraPaginaLista    ( false );
        $obPDF->setAlinhamento          ( "L" );
        $obPDF->addCabecalho            ( "OBS/JUSTIFICATIVA"   ,  100, 5, 'B', '', 'LTR','');
        $obPDF->setAlinhamento          ( "L" );
        $obPDF->addCampo                ( "observacaoSolicitacao"       , 8, '', '', 'LRB' );
        $rsRecordSetObservacao->setPrimeiroElemento();

        unset($arDescricaoObservacao);
        unset($arObsJustifLinhasSeparadas);
        unset($arObsJust);
    }

    //espaçamento
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho      ( 3 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""           ,100, -5);
    $obPDF->addCampo       ( ""      , 8 ,'','','LRBT');

    //Lista de itens
    $obPDF->addRecordSet( $rsRecordSet[6] );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Item"           ,5 , 6, 'B','','LRBT','205,206,205');
    $obPDF->addCabecalho   ( "Quantidade"     ,15, 6, 'B','','LRBT','205,206,205');
    $obPDF->addCabecalho   ( "Unidade"        ,10, 6, 'B','','LRBT','205,206,205');
    $obPDF->addCabecalho   ( "Especificação"  ,40, 6, 'B','','LRBT','205,206,205');
    $obPDF->addCabecalho   ( "Valor Unitário" ,15, 6, 'B','','LRBT','205,206,205');
    $obPDF->addCabecalho   ( "Valor Total"    ,15, 6, 'B','','LRBT','205,206,205');

    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "num_item"     , 8 ,'','','LRBT');
    $obPDF->addCampo       ( "quantidade"   , 8 ,'','','LRBT');
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "unidade"      , 8 ,'','','LRBT');
    $obPDF->addCampo       ( "nom_item"     , 8 ,'','','LRBT');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "vl_unitario"  , 8 ,'','','LRBT');
    $obPDF->addCampo       ( "vl_total"     , 8 ,'','','LRBT');

    $obPDF->addRecordSet( $rsRecordSet[7] );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlturaCabecalho      ( 0 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""           ,85, -5);
    $obPDF->addCabecalho   ( ""           ,15, -5);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "titulo"      , 8 ,'','','LRBT');
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ( "total_geral" , 8 ,'','','LRBT','205,206,205');

    //espaçamento
    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlturaCabecalho      ( 3 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""           ,100, -5);
    $obPDF->addCampo       ( ""      , 8 ,'','','LRBT');

    $obPDF->addRecordSet( $rsRecordSet[8] );
    $inNumLinhas = $rsRecordSet[6]->getNumLinhas();

    $inDivisao = (int) (($inNumLinhas - 29) / 46);
    $inResultado = 0;

    // 29 é o número de itens da primeira página
    // 46 é o número de linhas que cabem em uma página a partir da segunda página
    if ($inDivisao == 0) {
        $inResultado =  $inNumLinhas - 29;
    } else {
        $inResultado = (46 * $inDivisao) - ($inNumLinhas - 29);
    }

    if ( ($inNumLinhas > 16 && $inNumLinhas < 26)
      || ($inResultado > 34 && $inResultado < 44) ){
        $obPDF->setQuebraPaginaLista( true );
    } else {
        $obPDF->setQuebraPaginaLista( false );
    }

    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "L" );
    //      $obPDF->addCabecalho   ( ""           ,100, 5);
    $obPDF->addCabecalho   ( ""           ,100, 5, '','','LRT');

    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "data_autorizacao"      , 8 , '','','LR');

    $obPDF->addRecordSet( $rsRecordSet[9] );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""           ,50, 5, '','','L');
    $obPDF->addCabecalho   ( ""           ,50, 5, '','','R');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "autorizo"      , 8 ,'','','L');
    $obPDF->addCampo       ( "empenho"      , 8  ,'','','R');

    $obPDF->addRecordSet( $rsVazio );
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlturaCabecalho      ( 5 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( ""  ,50, 8, '','','LB');
    $obPDF->addCabecalho   ( "" ,50, 8, '','','RB');

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( ""      , 8 ,'','','');
    $obPDF->addCampo       ( ""      , 8 ,'','','');

    $inCount++;

    Sessao::write('filtro', $arFiltro);

}

$obPDF->arDatas = $arDt;
$obPDF->show();

?>
