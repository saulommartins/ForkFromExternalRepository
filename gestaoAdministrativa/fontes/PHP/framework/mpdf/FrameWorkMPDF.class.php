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
  * Página Oculta para gerar o arquivo Demostrativo RCL
  * Data de Criação: 24/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: FrameWorkMPDF.class.php 66548 2016-09-21 13:05:07Z evandro $
  * $Date: 2016-09-21 10:05:07 -0300 (Wed, 21 Sep 2016) $
  * $Author: evandro $
  * $Rev: 66548 $
  *
*/

include 'mpdf.php';
include 'viewloader/ViewLoader.class.php';
include CAM_GA_ADM_MAPEAMENTO.'TAdministracaoMPDF.class.php';

class FrameWorkMPDF
{
    /**
    * 
    * @var string
    *    
    */
    private $stPrincipalHTML;
    
    /**
    * 
    * @var string
    *    
    */
    private $stDiretorioArquivo;
    
    /**
    * 
    * @var string
    *    
    */
    private $stCabecalhoHTML;
    
    /**
    * 
    * @var string
    *    
    */
    private $stRodapeHTML;
    
    /**
    * 
    * @var string
    *    
    */
    private $stFolhaCSS;
    
    /**
    * 
    * @var string
    *    
    */
    private $stNomeRelatorio;
    
    /**
    * 
    * @var string
    *    
    */
    private $stCodEntidades;
    
    /**
    * 
    * @var string
    *    
    */
    private $stDataInicio;
    
    /**
    * 
    * @var string
    *    
    */
    private $stDataFinal;
    
    /**
    * 
    * @var string
    *    
    */
    private $stTipoSaida = 'I';
    
    /**
    * 
    * @var string
    *    
    */
    private $stFormatoFolha = 'A4';
    
    /**
    * 
    * @var string
    *    
    */
    private $stNomeArquivo;
    
    /**
    * 
    * @var integer
    *    
    */
    private $inCodGestao;
    
    /**
    * 
    * @var integer
    *    
    */
    private $inCodModulo;
    
    /**
    * 
    * @var integer
    *    
    */
    private $inCodRelatorio;
    
    /**
    * 
    * @var Array()
    *    
    */
    private $arConteudo;
    
    /**
    * 
    * @var boolean
    *    
    */
    private $boCabecalho;
    
    /**
    * 
    * @var boolean
    *    
    */
    private $boRodape;

    /**
    * 
    * @var Objeto
    *    
    */
    private $obMPDF;
    
    /**
    * 
    * @var Objeto
    *    
    */
    private $obViewLoader;
    
    public function getPrincipalHTML(){ return $this->stPrincipalHTML; }
    public function setPrincipalHTML( $stPrincipalHTML ) { $this->stPrincipalHTML = $stPrincipalHTML; }
    
    public function getCabecalhoHTML() { return $this->stCabecalhoHTML; }
    public function setCabecalhoHTML( $stCabecalhoHTML ) { $this->stCabecalhoHTML = $stCabecalhoHTML; }
    
    public function getRodapeHTML() { return $this->stRodapeHTML; }
    public function setRodapeHTML( $stRodapeHTML ) { $this->stRodapeHTML = $stRodapeHTML; }
    
    public function getCodGestao(){ return $this->inCodGestao; }
    public function setCodGestao( $inCodGestao ) { $this->inCodGestao = $inCodGestao; }
    
    public function getCodModulo(){ return $this->inCodModulo; }
    public function setCodModulo( $inCodModulo ) { $this->inCodModulo = $inCodModulo; }

    public function getCodRelatorio(){ return $this->inCodRelatorio; }
    public function setCodRelatorio( $inCodRelatorio ) { $this->inCodRelatorio = $inCodRelatorio; }
    
    public function getConteudo() { return $this->arConteudo; }
    public function setConteudo( $arConteudo ) { $this->arConteudo = $arConteudo; }
    
    public function getDiretorioArquivo() { return $this->stDiretorioArquivo; }
    public function setDiretorioArquivo( $stDiretorioArquivo ) { $this->stDiretorioArquivo = $stDiretorioArquivo; }
    
    public function getNomeArquivo() { return $this->stNomeArquivo;}
    public function setNomeArquivo( $stNomeArquivo ) { $this->stNomeArquivo = $stNomeArquivo; }
    
    public function getFolhaCSS() { return $this->stFolhaCSS; }
    public function setFolhaCSS( $stFolhaCSS ) { $this->stFolhaCSS = $stFolhaCSS; }
    
    public function getNomeRelatorio() { return $this->stNomeRelatorio; }
    public function setNomeRelatorio( $stNomeRelatorio ) { $this->stNomeRelatorio = $stNomeRelatorio; }
    
    public function getCodEntidades() { return $this->stCodEntidades; }
    public function setCodEntidades( $stCodEntidades ) { $this->stCodEntidades = $stCodEntidades; }
    
    public function getDataInicio() { return $this->stDataInicio; }
    public function setDataInicio( $stDataInicio ) { $this->stDataInicio = $stDataInicio; }

    public function getDataFinal() { return $this->stDataFinal; }
    public function setDataFinal( $stDataFinal ) { $this->stDataFinal = $stDataFinal; }
    
    public function getFormatoFolha() { return $this->stFormatoFolha; }
    /* recebera A4 para retrato ou A4-L para paisagem */
    public function setFormatoFolha( $stFormatoFolha ) { $this->stFormatoFolha = $stFormatoFolha; }
    
    public function getTipoSaida() { return $this->stTipoSaida; } 
    public function setTipoSaida( $stTipoSaida ) { $this->stTipoSaida = $stTipoSaida; }
    
    public function getMostraCabecalho() { return $this->boCabecalho; } 
    public function setMostraCabecalho( $valor ) { $this->boCabecalho = $valor; }
    
    public function getMostraRodape() { return $this->boRodape; } 
    public function setMostraRodape( $valor ) { $this->boRodape = $valor; }

    /**
     *
     * $method contruct
     * 
     * Metodo construtor da class FrameWorkMPDF
     * 
     */
    public function FrameWorkMPDF( $inCodGestao, $inCodModulo, $inCodRelatorio = '' )
    {
        $this->setCodGestao( $inCodGestao );
        $this->setCodModulo( $inCodModulo );

        $stLinkCSS = file_get_contents("../../../../../../gestaoAdministrativa/fontes/RPT/framework/MPDF/style.css");

        $this->setFolhaCSS ( $stLinkCSS );

        if($inCodRelatorio != ''){
            $this->setCodRelatorio( $inCodRelatorio );

            $this->preencheDiretorioArquivo();
            $this->preencheNomeArquivo();
    
            $this->obViewLoader = new ViewLoader($this->getDiretorioArquivo());
        }
        
        $this->setMostraCabecalho( TRUE );
        $this->setMostraRodape( TRUE );
    }
    
    /**
    * 
    * @method preencheDiretorioArquivo
    *
    * Metodo para pegar o diretório do arquivo de layout
    *
    * Ex: [gestao]/fonte/RPT/[modulo]/MPDF/
    *    
    */
    public function preencheDiretorioArquivo()
    {
        $stDiretorioGestao = SistemaLegado::pegaDado('nom_diretorio', 'administracao.gestao', 'where cod_gestao=' . $this->inCodGestao);
        $stDiretorioGestao = substr($stDiretorioGestao, 0, strlen($stDiretorioGestao) -4) . 'RPT/';
        $stDiretorioModulo = SistemaLegado::pegaDado('nom_diretorio', 'administracao.modulo', 'where cod_modulo=' . $this->inCodModulo);
        
        $this->setDiretorioArquivo( $stDiretorioGestao . $stDiretorioModulo . 'MPDF/' );
    }
    
    /**
    * 
    * @method preencheNomeArquivo
    *
    * Metodo para pegar o nome do arquivo de layout
    *
    * Ex: LHReceitas.php
    *    
    */
    public function preencheNomeArquivo()
    {
        $stNomeArquivoRelatorio = SistemaLegado::pegaDado('arquivo', 'administracao.relatorio', " where cod_gestao=".$this->getCodGestao()." and cod_modulo=".$this->getCodModulo()."and cod_relatorio=".$this->getCodRelatorio());
        $this->setNomeArquivo( $stNomeArquivoRelatorio );
    }
    
    /**
    * 
    * @method montaCabecalhoHTML
    *
    * Metodo para criar o HTML do cabeçalho do relatório
    *    
    */
    public function montaCabecalhoHTML()
    {
        $obCabecalho = new ViewLoader("../../../../../../gestaoAdministrativa/fontes/RPT/framework/MPDF/");
        
        if ( $this->getDataInicio() != "" AND $this->getDataFinal() != '' ) {
            $stCampoPeriodo = "Período: ".$this->getDataInicio()." até ".$this->getDataFinal();
        } else {
            $stCampoPeriodo = "Exercício : ". Sessao::getExercicio();
        }
        
        
        $arCabecalhoRelatorio = array( "arDadosRelatorio" => $this->preparaDadosCabecalhoRelatorio(),
                                       "arDadosEntidade"  => $this->preparaDadosCabecalhoEntidade(),
                                       "stCampoPeriodo"   => $stCampoPeriodo
                                      );
        
        $this->setCabecalhoHTML($obCabecalho->loadTemplate("LHCabecalho.php", $arCabecalhoRelatorio, false));
    }
    
    /**
    * 
    * @method preparaDadosCabecalhoRelatorio
    *
    * Metodo para preparar os dados do relatório que ser colocado no cabeçalho
    *    
    */
    public function preparaDadosCabecalhoRelatorio()
    {
        $rsRelatorio = new RecordSet();
        
        $obTAdministracaoMPDF = new TAdministracaoMPDF();
        $obTAdministracaoMPDF->setDado('cod_acao', Sessao::read("acao"));
        $obTAdministracaoMPDF->recuperaDadosRelatorio($rsRelatorio,'','',$boTransacao);
        
        if ( $rsRelatorio->getNumLinhas() > 0 ) {
            $arElementos = $rsRelatorio->getElementos();
        } else {
            $arElementos[0] = array("nom_acao" => "",
                                    "nom_funcionalidade" => "",
                                    "nom_modulo" => "",
                                    "versao" => "" );
        }
        
        return $arElementos;
    }

    /**
    * 
    * @method preparaDadosCabecalhoEntidade
    *
    * Metodo para preparar os dados da entidade que ser colocado no cabeçalho
    *    
    */
    public function preparaDadosCabecalhoEntidade()
    {
        $rsEntidade = new RecordSet();
        $obTAdministracaoMPDF = new TAdministracaoMPDF();
        $obTAdministracaoMPDF->setDado('exercicio', Sessao::getExercicio());
        
        // Quando for selecionada duas ou mais entidades para gerar o relatório, será verificado qual entidade está como prefeitura na tabela administracao.configuracao
        // Caso contrário será gerado com a entidade selecionada
        $inCodEntidade = $this->getCodEntidades();
        
        if(substr_count($inCodEntidade, ',') > 0 || empty($inCodEntidade)) {
            $inCodEntidade = SistemaLegado::pegaDado('valor','administracao.configuracao',"where cod_modulo = 8 AND parametro ILIKE 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."'");
            $obTAdministracaoMPDF->setDado("cod_entidade", $inCodEntidade);
        } else {
            $obTAdministracaoMPDF->setDado("cod_entidade", $inCodEntidade[0]);
        }
        
        $obTAdministracaoMPDF->recuperaDadosEntidade($rsEntidade,'','',$boTransacao);
        
        if ( $rsEntidade->getNumLinhas() > 0 ) {
            $arElementos = $rsEntidade->getElementos();
        } else {
            $arElementos[0] = array( "num_entidade" => "",
                                     "nom_entidade" => "",
                                     "e_mail" => "",
                                     "fone" => "",            
                                     "logradouro" => "",
                                     "cep" => "",
                                     "cnpj" => "",
                                     "logotipo" => ""
                                   );
        }
        
        return $arElementos;
        
    }
    
    /**
    * 
    * @method montaRodapeHTML
    *
    * Metodo para montar o conteudo do rodapé
    *    
    */
    public function montaRodapeHTML()
    {
        $obRodape = new ViewLoader("../../../../../../gestaoAdministrativa/fontes/RPT/framework/MPDF/");
        
        $arRodape = array( "stRodape" => "URBEM - Soluções Integradas de Administração Municipal - www.urbem.cnm.org.br" );
        
        $this->setRodapeHTML($obRodape->loadTemplate("LHRodape.php", $arRodape, false));
    }

    public function getDownloadNomeRelatorio() {
        $stNomRelatorio = $this->getNomeRelatorio();
        SistemaLegado::removeAcentosSimbolos($stNomRelatorio);
        $stNomRelatorio = ucwords( $stNomRelatorio );
        $stNomRelatorio = preg_replace("/[^a-zA-Z0-9]/","", $stNomRelatorio );

        return $stNomRelatorio."_".date("Y-m-d",time())."_".date("Hi",time()).".pdf";
    }

    /**
    * 
    * @method gerarRelatorio
    *
    * Metodo para gerar o relátorio em PDF
    *    
    */
    public function gerarRelatorio($stHtml = '', $stHtmlCabecalho = '')
    {
        // mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
        $mgt = 35;
        $mgb = 16;
        $mgh = 9;
        $mgf = 9;
        if($stHtml!='' && $stHtmlCabecalho!=''){
            $inLinhasCabecalho = substr_count($stHtmlCabecalho, '<tr>');
            $mgt = $mgt + (4 * $inLinhasCabecalho);
        }

        if(!$this->getMostraCabecalho()){
            $mgt = 1;
            $mgb = 1;
            $mgh = 1;
            $mgf = 1;           
        }

        $this->obMPDF = new mPDF($mode='',$format=$this->getFormatoFolha(),$default_font_size=0,$default_font='',$mgl=5,$mgr=7,$mgt,$mgb,$mgh,$mgf, $orientation='P');
        
        // converte o arquivo LHarquivo.php para código HTML
        if($stHtml=='')
            $this->setPrincipalHTML($this->obViewLoader->loadTemplate($this->getNomeArquivo(), $this->getConteudo(), false));
        else
            $this->setPrincipalHTML($stHtml);

        // Monta o Cabeçalho e o Rodapé do relatório
        if($this->getMostraCabecalho())
            $this->montaCabecalhoHTML();
        $this->setCabecalhoHTML($this->getCabecalhoHTML().$stHtmlCabecalho);

        if($this->getMostraRodape()){
            $this->montaRodapeHTML();
        }
        
        // recebe o HTML que foi gerado do cabecalho e do rodape e inseri na classe mPDF
        $this->obMPDF->SetHTMLHeader($this->getCabecalhoHTML());
        $this->obMPDF->SetHTMLFooter($this->getRodapeHTML());

        // Adiciona a folha de estilo ao relatório
        $this->obMPDF->WriteHTML($this->getFolhaCSS(), 1);

        // Adiciona o código HTMl do relatório, que será gerado pelos arquivo LHarquivo.php
        $this->obMPDF->WriteHTML($this->getPrincipalHTML(), 2);

        //Setando parametros para melhorar o desempenho da escrita PDF..
        error_reporting(0);
        $this->obMPDF->useSubstitutions = false; 
        $this->obMPDF->simpleTables = true;

        //Se for do tipo salvar o arquivo = , deve salvar na pasta tmp do urbem
        if ($this->getTipoSaida() == "F") {
            $stCaminhoArquivo = CAM_FW_TMP.$this->getDownloadNomeRelatorio();
        } else {
            $stCaminhoArquivo = $this->getDownloadNomeRelatorio();
        }

        $this->obMPDF->Output( $stCaminhoArquivo , $this->getTipoSaida());
    }
}

?>