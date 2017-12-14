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
  * Carnê IPTU Urbem Presidente Fiqueiredo
  * Data de criação : 09/09/2016

  * @author Analista: Fábio Bertoldi e Luciana Dellay
  * @author Programador: Evandro Melos

  $Revision: $
  $Name: $
  $Author: $
  $Date: $
  
*/
include_once CAM_GT_ARR_CLASSES.'boletos/RCodigoBarraFebraban.class.php';
include_once CAM_GT_ARR_NEGOCIO.'RARRCarne.class.php';
include_once CAM_GT_ARR_NEGOCIO.'RARRConfiguracao.class.php';

class RCarneIptuPresidenteFigueiredo
{

var $arEmissao;
var $obRARRCarne;

public function setArEmissao($valor){ $this->arEmissao = $value; }

public function getArEmissao(){ return $this->arEmissao; }

public function setobRARRCarne($valor){ $this->obRARRCarne = $value; }

public function getobRARRCarne(){ return $this->obRARRCarne; }

/*
    * Metodo Construtor
    * @access Private
*/
public function __construct($arEmissao)
{
    $this->obRARRCarne      = new RARRCarne();
    $this->arEmissao        = $arEmissao;    
}

function percentageBar($nuPercentual,$stMensagem="")
{
    $stBarra  = "<div id=\"box\" style=\"width:500px;border:2px solid #fff;height:17px;text-align:center;\">";
    $stBarra .= $nuPercentual."%";
    $stBarra .= "<div id=\"bar\" style=\"width:".str_replace(',','.',$nuPercentual)."%;background:#FF8C00;height:14px;color:#fff;text-align:right;padding:3px 0px 0px 0px;margin-top:-19px\">";
    $stBarra .= "</div>";
    $stBarra .= "</div>";
    $stJs = "<script>";
    $stJs .= "jQuery('#loadingModal',parent.frames[2].document).attr('style','visibility:hidden;');";
    $stJs .= "jQuery('#showLoading h5',parent.frames[2].document).html('".$stBarra.$stMensagem."');";
    $stJs .= "</script>";
    echo $stJs;
    flush();
}

public function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;

    $this->obRARRConfiguracao     = new RARRConfiguracao;
    $this->obRARRConfiguracao->setCodModulo ( 2 );
    $this->obRARRConfiguracao->consultar();
    $inCodFebraban = $this->obRARRConfiguracao->getCodFebraban();
    unset($this->obRARRConfiguracao);

    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
    $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );
    $stNomeImagem = $rsListaImagens->getCampo("valor");
    $inCountTemp = 0;

    $obTARRCarne = new TARRCarne;
    $inTotalDeCarnes = count( $this->arEmissao );
    $inCarneAtual = 1;

    $index = 0;    
    foreach ($this->arEmissao as $valor => $chave) {        
        for ($index; $index < count($chave); $index++) { 
        
        $flTotalImpresso = round( ( $inCarneAtual * 100 ) / $inTotalDeCarnes, 2 );
        $this->percentageBar( $flTotalImpresso, "Processando..." );
        $inCarneAtual++;
        unset( $rsListaCarnePrefeitura );
        $obTARRCarne->recuperaDadosPrefeituraIPTUGenerico( $rsListaCarnePrefeitura, Sessao::getExercicio() );
        
        include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";
        $obTAdministracaoUsuario = new TAdministracaoUsuario();
        $stFiltro = " WHERE usuario.status = 'A'
                    AND usuario.username = '".Sessao::read('stUsername')."' ";
        $obTAdministracaoUsuario->recuperaUsuario($rsUsuario, $stFiltro);

        //Add dados cabecalho
        $arDadosRelatorio[$index]['cabecalho']['nom_prefeitura'] = $rsListaCarnePrefeitura->getCampo('nom_prefeitura');
        $arDadosRelatorio[$index]['cabecalho']['nom_secretaria'] = $rsListaCarnePrefeitura->getCampo('carne_secretaria');
        $arDadosRelatorio[$index]['cabecalho']['carne_dam']      = $rsListaCarnePrefeitura->getCampo('carne_dam');
        $arDadosRelatorio[$index]['cabecalho']['imagem_brasao']  = CAM_FW_TEMAS."imagens/".$stNomeImagem;
        $arDadosRelatorio[$index]['cabecalho']['usuario']        = $rsUsuario->getCampo('nom_cgm');
                    
            unset( $rsListaDetalheCreditos );
            $obErro = $obTARRCarne->ListaDadosPorCreditoParaCarneIPTU( $rsListaDetalheCreditos, " WHERE al.cod_lancamento = ".$valor." AND ap.cod_parcela = ".$chave[$index]['cod_parcela']." AND carne.numeracao = '".$chave[$index]['numeracao']."'" );
            $arTMPDados = $rsListaDetalheCreditos->getElementos();

            $flTotalCreditosDesc = 0;
            $flTotalCreditosValor = 0;
            for ( $inX=0; $inX<count( $arTMPDados ); $inX++ ) {
                $flTotalCreditosValor += $arTMPDados[$inX]["valor_credito"];
                if ( $arTMPDados[$inX]["desconto"] == 't' )
                    $flTotalCreditosDesc += $arTMPDados[$inX]["valor_credito"];
            }

            if ( $arTMPDados[0]["desconto_parcela"] > 0 )
                $flTotalDesconto = $flTotalCreditosValor - $arTMPDados[0]["desconto_parcela"];
            else
                $flTotalDesconto = 0.00;

            for ( $inX=0; $inX<count( $arTMPDados ); $inX++ ) {
                if ($arTMPDados[$inX]["desconto"] == 't') {
                    $arTMPDados[$inX]["desconto_credito"] = ( $arTMPDados[$inX]["valor_credito"] * 100 ) / $flTotalCreditosDesc;
                    $arTMPDados[$inX]["desconto_credito"] = ( $flTotalDesconto * $arTMPDados[$inX]["desconto_credito"] ) / 100;
                }else
                    $arTMPDados[$inX]["desconto_credito"] = 0.00;
            }

            $rsListaDetalheCreditos->preenche( $arTMPDados );
        

        if ( $obErro->ocorreu() ) {
            break;
        }

        unset( $arDados );
        $arDados = array();                
        $stNumeracao = $chave[$index]['numeracao'];
        $stVencimento = $rsListaDetalheCreditos->getCampo( 'vencimento' );
        $arDados[$index]["vencimento"] = $rsListaDetalheCreditos->getCampo( 'vencimento' );            
        $stTributo = $rsListaDetalheCreditos->getCampo( 'descricao' );            
        unset($flValorTotalCorrecao);
        unset($flValorTotalMulta);
        unset($flValorTotalJuros);
        unset($flValorTotal);
        while ( !$rsListaDetalheCreditos->Eof() ) {
            $flValorTotalDesc += $rsListaDetalheCreditos->getCampo("desconto_credito");
            $flValorTotal += $rsListaDetalheCreditos->getCampo("valor_credito");
            $flValorTotalJuros += $rsListaDetalheCreditos->getCampo("credito_juros_pagar");
            $flValorTotalMulta += $rsListaDetalheCreditos->getCampo("credito_multa_pagar");
            $flValorTotalCorrecao += $rsListaDetalheCreditos->getCampo("credito_correcao_pagar");         

            $rsListaDetalheCreditos->proximo();            
        }

        $rsListaDetalheCreditos->setPrimeiroElemento();
        unset($flValorTotalGeral);
        $flValorTotalGeral += $flValorTotalCorrecao + $flValorTotalMulta + $flValorTotalJuros + ($flValorTotal-$flValorTotalDesc);

        unset( $this->obBarra );
        $this->obBarra = new RCodigoBarraFebraban;
        unset( $this->arBarra );
        $this->arBarra = array();

        $nuValorTotal = $flValorTotalGeral;
        $nuValorNormal = $flValorTotal;
        $stJuroNormal = $flValorTotalJuros;
        $stMultaNormal = $flValorTotalMulta;
      
        if ($diffBaixa) {
            $this->arBarra['tipo_moeda'] = 6;
        } else {
            if ( $rsListaDetalheCreditos->getCampo( 'nr_parcela' ) == 0 ) {
                $this->arBarra['tipo_moeda'] = 6;
            } else {
                $this->arBarra['tipo_moeda'] = 7;
            }
        }

        $this->arBarra['valor_documento'] = number_format( $nuValorTotal, 2, '.', '' );
        $this->arBarra['vencimento'] = (string) $rsListaDetalheCreditos->getCampo( 'vencimento' );
        $this->arBarra['nosso_numero'] = (string) $rsListaDetalheCreditos->getCampo( 'numeracao' );            
        $this->arBarra['cod_febraban'] = $inCodFebraban;
        
        unset( $this->arCodigoBarra );
        $arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
        
        foreach ($rsListaDetalheCreditos->getElementos() as $value) {
            $stTributo = $value['descricao_credito'];
            $inNumParcela = $value['numeracao_parcela'];
            $stDataVencimento = $value['vencimento'];
            break;
        }

        unset($obTCIMImovel);
        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                    );
        $obTCIMImovel = new TCIMImovel();
        $obErro = $obTCIMImovel->recuperaRelacionamento($rsImovel," WHERE IMOVEL.inscricao_municipal = ".$chave[$index]['inscricao']." ","",$boTransacao);
        $stEnderenco = $rsImovel->getCampo('logradouro').' '.$rsImovel->getCampo('numero').' '.$rsImovel->getCampo('complemento').' '.$rsImovel->getCampo('nom_bairro').' - '.$rsImovel->getCampo('nom_localizacao').' - LT '.$rsImovel->getCampo('valor');
        
        $obTARRCarne->recuperaDadosValorVenalIPTUGenericoUrbem( $rsListaCarne, $chave[$index]['inscricao'], $chave[$index]['exercicio'], $chave[$index]['cod_parcela'], $boTransacao);
        
        $stValorBase = number_format($rsListaCarne->getCampo('venal_total_calculado'),2,',','.');

        //Add dados para o carne
        $arDadosRelatorio[$index]['dados']['exercicio']             = $chave[$index]["exercicio"];
        $arDadosRelatorio[$index]['dados']['inscricao_cadastral']   = $rsImovel->getCampo('mat_registro_imovel');
        $arDadosRelatorio[$index]['dados']['sp']                    = 0;
        $arDadosRelatorio[$index]['dados']['aliquota']              = "0%";
        $arDadosRelatorio[$index]['dados']['tributo']               = $stTributo;
        $arDadosRelatorio[$index]['dados']['num_parcela']           = $inNumParcela;
        $arDadosRelatorio[$index]['dados']['data_emissao']          = date('d/m/Y');
        $arDadosRelatorio[$index]['dados']['inscricao']             = $chave[$index]['inscricao'];
        $arDadosRelatorio[$index]['dados']['numeracao']             = $chave[$index]['numeracao'];
        $arDadosRelatorio[$index]['dados']['contribuinte']          = SistemaLegado::pegaDado("nom_cgm","sw_cgm","where numcgm = ".$chave[$index]['numcgm']." ",$boTransacao);
        $arDadosRelatorio[$index]['dados']['codigo_barra']          = $arCodigoBarra['codigo_barras'];
        $arDadosRelatorio[$index]['dados']['vencimento']            = $stDataVencimento;
        $arDadosRelatorio[$index]['dados']['processamento']         = $chave[$index]['numeracao']."/".$chave[$index]["exercicio"];
        $arDadosRelatorio[$index]['dados']['endereco']              = $stEnderenco;
        $arDadosRelatorio[$index]['dados']['base_calculo']          = $stValorBase;
        $arDadosRelatorio[$index]['dados']['especificacao_receita'] = "IMPOSTO SOBRE A PROPRIEDADE PREDIAL E TERRITORIAL URBANA";
        $arDadosRelatorio[$index]['dados']['informacoes']           = "NÃO RECEBER APÓS O VENCIMENTO.";
        $arDadosRelatorio[$index]['dados']['linha_digitavel']       = $arCodigoBarra['linha_digitavel'];
                
        foreach ($rsListaDetalheCreditos->getElementos() as $value) {
            $arDadosRelatorio[$index]['valores']['credito'][$value['cod_credito']] = $value['valor_credito'];
            
        }
        $arDadosRelatorio[$index]['valores']['juros'] = number_format( $stJuroNormal, 2, ',', '.' );
        $arDadosRelatorio[$index]['valores']['multa'] = number_format( $stMultaNormal, 2, ',', '.' );
        $arDadosRelatorio[$index]['valores']['total'] = number_format( $nuValorTotal, 2, ',', '.' );

    }//FIM FOR por numero de parcelas    
    } // fim foreach $arEmissao
        
    
    $arDadosEmissao['arDados'] = $arDadosRelatorio;
    Sessao::write('arDados',$arDadosEmissao);
    
    SistemaLegado::LiberaFrames();
    
    Sessao::write( 'stNomPdf','CarneIPTUPresidenteFigueiredo.pdf' );
    $this->LancaRelatorio(); // lanca o pdf
}

public function LancaRelatorio()
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once CLA_MPDF;
    
    $obMPDF = new FrameWorkMPDF(5,25,8);
    $stNomeRelatorio = Sessao::read( 'stNomPdf' );
    $obMPDF->setNomeRelatorio($stNomeRelatorio);
    
    Sessao::read('arDados');
    
    $obMPDF->setMostraCabecalho(false);
    $obMPDF->setMostraRodape(false);
    $obMPDF->setTipoSaida("F");
    $obMPDF->setFormatoFOlha("A4-L");    
    $obMPDF->setConteudo(Sessao::read('arDados'));
    $obMPDF->gerarRelatorio();
    $stNomeRelatorioDownload = $obMPDF->getDownloadNomeRelatorio();
    Sessao::write('stNomeRelatorioDownload',$stNomeRelatorioDownload);

}

}//CLASS END