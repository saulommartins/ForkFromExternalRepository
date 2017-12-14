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
 /**********************************************************************
 *
 * Classe que auxilia na montagem de filtros e geração do preview de relatorios do Birt
 *
 * Data de Criação: 28/12/2006
 *
 * @author Analista: Lucas Stephanou
 * @author Desenvolvedor: Lucas Stephanou
 *
 * $Id: PreviewBirt.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $
 * Casos de uso: uc-01.00.00
 *
 ***********************************************************************/
/**
* Classe geradora do Preview dos Relatórios do BIRT
*/
class PreviewBirt
{
    /**
     * Codigo da Gestao
     *
     * @var int
     */
    public $inCodGestao;
    /**
     * Código do Módulo
     *
     * @var int
     */
    public $inCodModulo;
    /**
     * Código do Relatório
     *
     * @var int
     */
    public $inCodRelatorio;
    /**
     * Parametros a serem passados ao relatorio
     *
     * @var array
     */
    public $arParametros;
    /**
     * Nome do Arquivo a ser gerado
     *
     * @var string
     */
    public $stNomeArquivo;
    /**
     * Nome do Arquivo .rptdesign a ser visualizado
     *
     * @var string
     */
    public $stNomeRelatorio;
    /**
     * Titulo para o Relatorio
     *
     * @var string
     */
    public $stTitulo;
    /**
     * Formato de Saida (HTML/PDF)
     *
     * @var string
     */
    public $stFormato;
    /**
     * Define a versao de viewer do Birt
     *
     * @var String
     */
    public $stVersao;
    /**
     * Filtro para registro no banco
     *
     * @var string
     */
    public $stFiltro;

    /**
     * Flag para exibir botao de exportacao para o excel
     * @var boolean
     */
    public $boExportaExcel;

    /**
     * Flag para gerar o preview do relatório em uma popup
     * $var boolean
     */
    public $boPopup;

    /**
     * String que guarda a URL de retorno do relatorio
     * $var string
     */
    public $stReturnURL;

    public $boDownload = true;

    public $stDebug;

    /**
     * Seta geração do relatório em uma popup
     * @var $valor boolean true para Exibir false para não exibir
     */
    public function setPopup($valor)
    {
        $this->boPopup = $valor;
    }

    public function getDebug()
    {
        return $this->stDebug;
    }

    public function setDebug($valor=false)
    {
        $this->stDebug = $valor;
    }

    public function getPopup()
    {
        return $this->boPopup;
    }

    public function getDownload() { return $this->boDownload; }

    /**
     * Flag para exibir botao de exportacao para o word
     * @var boolean
     */
    public $boExportaWord;
    /**
     * Seta visualiação do botao de Exportacao para Word
     * @var $valor boolean <ul><li>true para Exibir</li><li>false para não exibir</li></ul>
     */

    public function setExportaWord($valor)
    {
        $this->boExportaWord = $valor;
    }

    public function getExportaWord()
    {
        return $this->boExportaWord;
    }

    public $boCodigoBarra;

    public function setCodigoBarra($valor)
    {
        $this->boCodigoBarra = $valor;
    }

    public function setDownload($valor) { $this->boDownload = $valor; }

    public function getCodigoBarra()
    {
        return $this->boCodigoBarra;
    }

    /**
     * Seta visualiação do botao de Exportacao para Excel
     * @var $valor boolean <ul><li>true para Exibir</li><li>false para não exibir</li></ul>
     */
    public function setExportaExcel($valor)
    {
        $this->boExportaExcel = $valor;
    }

    public function getExportaExcel()
    {
        return $this->boExportaExcel;
    }

    public function setNomeArquivo($valor)
    {
        $this->stNomeArquivo = $valor;

    }

    public function getNomeArquivo()
    {
        return $this->stNomeArquivo;

    }

    public function setTitulo($valor)
    {
        $this->stTitulo = $valor;

    }

    public function getTitulo()
    {
        return substr($this->stTitulo,0,85);
    }

    public function setNomeRelatorio($valor)
    {
        $this->stNomeRelatorio = $valor;

    }

    public function getNomeRelatorio()
    {
        return $this->stNomeRelatorio;

    }

    public function setFormato($valor)
    {
        $this->stFormato = $valor;

    }

    public function getFormato()
    {
        return $this->stFormato;

    }

    public function setFiltro($valor)
    {
        $this->stFiltro = $valor;

    }

    public function getFiltro()
    {
        return $this->stFiltro;

    }

    public function setVersaoBirt($valor)
    {
        $this->stVersao = $valor;

    }

    public function getVersaoBirt()
    {
        return $this->stVersao;

    }

    public function setReturnURL($valor)
    {
        $this->stReturnURL = $valor;

    }

    public function getReturnURL()
    {
        return $this->stReturnURL;

    }

    public function PreviewBirt($inCodGestao, $inCodModulo, $inCodRelatorio)
    {
        $this->arParametros = array();
        $this->setFormato("html");
        $this->setNomeArquivo(null);
        $this->setVersaoBirt("4.4.0");
        $this->addParametro("inCodGestao", $inCodGestao);
        $this->addParametro("inCodModulo", $inCodModulo);
        $this->addParametro("inCodRelatorio", $inCodRelatorio);

        $this->inCodGestao = $inCodGestao;
        $this->inCodModulo = $inCodModulo;
        $this->inCodRelatorio = $inCodRelatorio;
        $this->setFiltro(" where cod_gestao=" . $inCodGestao . " and cod_modulo=" . $inCodModulo . "and cod_relatorio=" . $inCodRelatorio);

        $this->setDebug(false);

    }
    /**
     * Adiciona parametro
     *
     * @param  string  $stNome
     * @param  string  $stValor
     * @access public
     * @return boolean
     */

    public function addParametro($stNome, $stValor)
    {
        if (in_array(array(
            "fmt",
            "filename",
            "reportLayout"
        ) , $this->arParametros)) {
            return false;

        }
        $this->arParametros[] = array(
            $stNome,
            $stValor
        );

        return true;

    }

    private function debug($stURL = null)
    {
        $arToDebug = $this->arParametros;

        // se passar a url, faz debug completo
        if (isset($stURL)) {
            $arToDebug = explode("&",$stURL);
        }

        if (is_array($arToDebug) && count($arToDebug)>0) {
            echo "<pre class='debug'>";
            foreach ($arToDebug as $key => $value) {
                if (isset($stURL) && strpos($value,'=')) {
                   $value = explode("=",$value);
                }
                echo "<b>".$value[0].": </b>";
                var_dump($value[1]);
            }
            echo "</pre>";
        }
    }

    /**
     * addAssinaturas
     *
     * Faz o processo para se passar os parâmetros necessários para gerar as assinaturas em um relatório do Birt.
     * É necessário passar 4 parâmetros para que seja montada as assinaturas (entidade_assinatura, numcgm_assinatura, timestamp_assinatura
     * e numero_assinatura).
     *
     * @param array $arAssinaturas array de assinaturas que é montado a partir do IMontaAssinaturas
     *
     * @access public
     * @return void
     */
    public function addAssinaturas($arAssinaturas)
    {
        $stEntidade = "";
        $stTimestamp = "";
        $stCGM = "";

        if (count($arAssinaturas['selecionadas']) > 0) {
            foreach ($arAssinaturas['selecionadas'] as $arSelecionadas) {
                $stEntidade  .= $arSelecionadas['inCodEntidade'].",";
                $stTimestamp .= "'".$arSelecionadas['timestamp']."',";
                $stCGM       .= $arSelecionadas['inCGM'].",";
            }
            $stEntidade = substr($stEntidade, 0, (strlen($stEntidade)-1));
            $stTimestamp = substr($stTimestamp, 0, (strlen($stTimestamp)-1));
            $stCGM = substr($stCGM, 0, (strlen($stCGM)-1));

            $this->addParametro('numero_assinatura'   , count($arAssinaturas['selecionadas']));
            $this->addParametro('entidade_assinatura' , $stEntidade );
            $this->addParametro('timestamp_assinatura', $stTimestamp);
            $this->addParametro('numcgm_assinatura'   , $stCGM);
        } else {
            $this->addParametro('numero_assinatura'   , 0);
            $this->addParametro('entidade_assinatura' , '');
            $this->addParametro('timestamp_assinatura', '');
            $this->addParametro('numcgm_assinatura'   , '');
        }

    }

    public function preview()
    {
        # Todos os relatórios do Urbem serão exibidos no Viewer 4.4.0
        $this->setVersaoBirt('4.4.0');

        # Instancia da classe Conexao
        $obConexao = new Conexao();

        $stBirtPdf = $stBirtDoc = $stBirtXls = $stForm = $stParametrosUrbem = "";

        // arquivo de design
        $stReportLayout = SistemaLegado::pegaDado('arquivo', 'administracao.relatorio', $this->getFiltro());

        if ( $this->getTitulo() && ($this->getTitulo() <> 'Relatório do Birt') ) {
            $stTitulo = $this->getTitulo();
        } else {
            $stTitulo = SistemaLegado::pegaDado('nom_relatorio', 'administracao.relatorio', $this->getFiltro());
        }

        $stDiretorioGestao = SistemaLegado::pegaDado('nom_diretorio', 'administracao.gestao', 'where cod_gestao=' . $this->inCodGestao);
        $stDiretorioGestao = substr($stDiretorioGestao, 0, strlen($stDiretorioGestao) -4) . 'RPT/';
        $stDiretorioModulo = SistemaLegado::pegaDado('nom_diretorio', 'administracao.modulo', 'where cod_modulo=' . $this->inCodModulo);
        $stBirtLayoutsFolder = $stDiretorioGestao . $stDiretorioModulo . 'report/design/';

        if ( !constant('BIRT_HOST') ) {
            if ( strpos($_SERVER['HTTP_HOST'], ":") >0 ) {
                $http_host = substr($_SERVER['HTTP_HOST'], 0, (strpos($_SERVER['HTTP_HOST'], ":") ));
            } else {
                $http_host = $_SERVER['HTTP_HOST'];
            }
        } else {
            $http_host = constant('BIRT_HOST');
        }

        # verifica se deve imprimir codigo de barras
        if (isset($this->boCodigoBarra)) {
            $this->addParametro("url", URBEM_ROOT_URL."gestaoAdministrativa/fontes/PHP/framework/barcode/index.php?altura=50&numeracao=");
        }

        # Adicionado suporte ao protocolo https para gerar os relatórios do Urbem.
        $boSecureConnection = false;
        $stProtocol = 'http';

        $stPort = constant('BIRT_PORT');
        
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
            $boSecureConnection = true;
            $stProtocol = 'https';
            $stPort = constant('BIRT_PORT_SSL');
        }
        
        if ($this->getVersaoBirt()) {
            $stBirt = $stProtocol . "://" . $http_host . ":" . $stPort . "/viewer_" . str_replace('.','',$this->getVersaoBirt()) . "/";
        } else {
            $stBirt = $stProtocol . "://" . $http_host . ":" . $stPort . "/viewer/";
        }

        $stBirt .= "run?";

        # Percorre o array de parâmetros vindos do Urbem para serem usados nos relatórios gerados pelo Birt.
        if (is_array($this->arParametros) && count($this->arParametros) > 0) {
            foreach ($this->arParametros as $parametro) {
                $stParametrosUrbem .= "<input type='hidden' name=\"". $parametro[0] ."\" value=\"". $parametro[1] ."\" />\n";
            }
        }
        if ($this->getFormato() == "pdf") {
             $stBirtPdfDireto.= $stBirt."__report=" . realpath($stBirtLayoutsFolder . $stReportLayout);

            // conexao
            $stBirtPdfDireto.= "&db_driver=" . urlencode('org.postgresql.Driver') . "";
            $stBirtPdfDireto.= "&term_user=" .Sessao::read('stUsername');
            $stBirtPdfDireto.= "&cod_acao=" . Sessao::read('acao');
            $stBirtPdfDireto.= "&exercicio=" . Sessao::getExercicio();
            $stBirtPdfDireto.= "&db_conn_url=" . urlencode("jdbc:postgresql://" . $obConexao->stHost . ":" . $obConexao->inPort . "/" . $obConexao->stDbName . "");
            // varre parametros
    
            foreach($this->arParametros as $parametro) {
                $stBirtPdfDireto.= "&" . $parametro[0] . "=" . urlencode($parametro[1]);

            }
            $stBirtPdfDireto.= "&__format=" . $this->getFormato();
            // formatação numérica para valores na moeda brasileira
            $stBirtPdfDireto.= "&__locale=pt_BR";
            
            // case seja pdf pega parametro de nome do arquivo ou
            $stBirtPdfDireto .= "filename=";
            $stBirtPdfDireto .= $this->getNomeArquivo() ? $this->getNomeArquivo() . "_" . date("Ymdhs") : "RelatorioUrbem_" . date("Ymdhs");
            $stBirtPdfDireto .= "." . $this->getFormato();

        }

        # Monta HTML com formulário de chamada ao Birt, ao fim submete o html para o frame do relatório.

        $stPreviewHtml  = "<html>\n";
        $stPreviewHtml .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
        $stPreviewHtml .= "<head>\n";
        $stPreviewHtml .= "<title> " . $this->getTitulo() . "</title>\n";
        $stPreviewHtml .= "<script src='../../../../../../gestaoAdministrativa/fontes/javaScript/scriptaculous/prototype.js' type='text/javascript'></script>\n";
        $stPreviewHtml .= "<script src='../../../../../../gestaoAdministrativa/fontes/javaScript/scriptaculous/scriptaculous.js' type='text/javascript'></script>\n";
        $stPreviewHtml .= "<script src='../../../../../../gestaoAdministrativa/fontes/javaScript/jquery.js' type='text/javascript'></script>\n";
        $stPreviewHtml .= "<script src='../../../../../../gestaoAdministrativa/fontes/javaScript/jquery-migrate-1.2.1.js' type='text/javascript'></script>\n";
        $stPreviewHtml .= "<link href='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/CSS/stylos_ns.css' rel='stylesheet' type='text/css' />\n";
        $stPreviewHtml .= "<link href='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/CSS/birt.css' rel='stylesheet' type='text/css' />\n";
        $stPreviewHtml .= "<style> body { background-color:#fff;} a {cursor:hand;} </style>\n";
        $stPreviewHtml .= "<script>     \n";
        $stPreviewHtml .= "   function inverteFrames() { \n";

        if ($this->getPopup()) {
            $stPreviewHtml .= "document.getElementById('iframe_birt_proc').style.display='none';\n";
            $stPreviewHtml .= "document.getElementById('iframe_birt').style.display='';\n";
        } else {
            $stPreviewHtml .= "parent.frames['telaPrincipal'].document.getElementById('iframe_birt_proc').style.display='none';\n";
            $stPreviewHtml .= "parent.frames['telaPrincipal'].document.getElementById('iframe_birt').style.display='';\n";
        }

        $stPreviewHtml .= "}\n";
        $stPreviewHtml .= "
            function shrinkFrame()
            {
                jQuery('#frOculto',parent.document).attr('rows','0,*,0,0');
                jQuery('#frOculto #frTela',parent.document).attr('cols','0,*');
                jQuery('#imgFechaPreview',parent.frames[2].document).attr('src','../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnEstornar32.png');
                jQuery('#imgFechaPreview',parent.frames[2].document).attr('title','Voltar ao Urbem ( Fechar Preview )!');

            }
            jQuery().ready(function () {
                shrinkFrame();
                jQuery('#imgFechaPreview',parent.frames[2].document).toggle(
                    function () {
                        jQuery('#frOculto',parent.document).attr('rows','77,*,22,0');
                        jQuery('#frOculto #frTela',parent.document).attr('cols','180,*');
                        jQuery('#imgFechaPreview',parent.frames[2].document).attr('src','../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnRelatorio_32.png');
                        jQuery('#imgFechaPreview',parent.frames[2].document).attr('title','Mostrar Ambiente de Visualização!');
                        url = jQuery('#imgFechaPreview',parent.frames[2].document).parent().attr('href');
                        if (url != '#') {
                            window.location.href = url;
                        }

                        return false;
                    },
                    function () {
                        shrinkFrame();

                        return false;
                    }
                );

            });

            function mandaFormulario()
            {
                document.relatorio.submit();
            }

            </script> ";

        $stPreviewHtml .= "</head>\n";
        $stPreviewHtml .= "<body onload='mandaFormulario();'> \n";
        $stPreviewHtml .= "<table width='100%' border=0>\n";
        $stPreviewHtml .= "     <tr>\n";
        $stPreviewHtml .= "         <td class='titulocabecalho' height='5' width='100%'>\n";
        $stPreviewHtml .= "             <table  cellspacing=0 cellpadding=0 class='titulocabecalho_gestao' width='100%'>\n";
        $stPreviewHtml .= "                 <tr>\n";
        $stPreviewHtml .= "                     <td width='80%'>Pré-visualização de Relatório</td>\n";
        $stPreviewHtml .= "                     <td width='20%' class='versao'>&nbsp;</td>\n";
        $stPreviewHtml .= "                 </tr>\n";
        $stPreviewHtml .= "             </table>\n";
        $stPreviewHtml .= "         </td>\n";
        $stPreviewHtml .= "    </tr>\n";
        $stPreviewHtml .= "</table>\n";

        # Formulário que submete ao relatório do Birt.
        $stFormBirt  = "<form  id='birtreport' name='relatorio' method='post'  target='iframe_birt' action='". $stBirt ."' accept-charset='utf-8'>";
        $stFormBirt .= "<input type='hidden' name='term_user'   value='".Sessao::read('stUsername')."' />\n";
        $stFormBirt .= "<input type='hidden' name='cod_acao'    value='".Sessao::read('acao')."' />\n";
        $stFormBirt .= "<input type='hidden' name='exercicio'   value='".Sessao::getExercicio()."' />\n";
        $stFormBirt .= "<input type='hidden' name='db_conn_url' value='jdbc:postgresql://" . $obConexao->stHost . ":" . $obConexao->inPort . "/" . $obConexao->stDbName . "' />\n";

        # Recupera caminho/nome do relatório
        $stFormBirt .= "<input type='hidden' name='__report' value='".realpath($stBirtLayoutsFolder . $stReportLayout)."' />\n";

        # Locale utilizado para formatação numérica de valores monetários.
        $stFormBirt .= "<input type='hidden' name='__locale' value='pt_BR' />\n";

        # Em qual formato o relatório deve ter sua saída.
        $stFormBirt .= "<input type='hidden' id='reportFormat' name='__format' value='".$this->getFormato()."' />\n";

        # Adiciona os filtros vindos do Urbem aos relatórios.
        $stFormBirt .= $stParametrosUrbem;

        $stFormBirt .=" </form>\n";

        # Atribui o formulário ao Preview do Birt
        $stPreviewHtml .= $stFormBirt;

        $stPreviewHtml .= " <div id='conteudo'>\n";
        $stPreviewHtml .= "     <div id='incTopo'>\n";
        $stPreviewHtml .= "         <ul id='menu'>\n";

        if ($this->getExportaExcel() || $this->getExportaWord()) {

            if ( $this->getExportaWord() ) {
                $stPreviewHtml .= " <li>
                                    <a target='iframe_birt' onclick=\"jQuery('#reportFormat').val('doc'); mandaFormulario();\">
                                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnDOC_32.png' alt='Visualizar como Documento do Word' border='0' />
                                    </a>
                                </li>";
            }

            if ( $this->getExportaExcel() ) {
                $stPreviewHtml .= " <li>
                                    <a target='iframe_birt' onclick=\"jQuery('#reportFormat').val('xls'); mandaFormulario();\">
                                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnXLS_32.png' alt='Visualizar como Documento do Excel' border='0' />
                                    </a>
                                </li>";
            }
        }

        $stPreviewHtml .= "             <li><a target='iframe_birt' onclick=\"jQuery('#reportFormat').val('pdf'); mandaFormulario();\"><img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnPDF_32.png' alt='PDF' border='0' /></a></li>";
        $stPreviewHtml .= "             <li><a href='" . ($this->getReturnURL() == '' ? '#' : URBEM_ROOT_URL . substr($this->getReturnURL(),18)) . "'><img id='imgFechaPreview' border='0' /></a></li>";
        $stPreviewHtml .= "         </ul>";
        $stPreviewHtml .= "         <dl>";
        $stPreviewHtml .= "             <dt>" . $stTitulo . "</dt>";
        $stPreviewHtml .= "             <dd class='defData'>Data: " . date("d/m/y - H:i") . "</dd>";
        $stPreviewHtml .= "         </dl>";
        $stPreviewHtml .= "     </div>\n";
        $stPreviewHtml .= "     <div id='corpo'>\n";
        $stPreviewHtml .= "         <iframe style='border:none;' src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/birt/instancias/Processando.php' id='iframe_birt_proc' name='iframe_birt_proc' width='98%' height='85%' longdesc='IFrame com Relatorio'></iframe>\n";
        $stPreviewHtml .= "            <iframe onLoad='inverteFrames();' style='border:none;display:none;' src'about:blank' id='iframe_birt' name='iframe_birt' width='98%' height='85%' longdesc='IFrame com Relatorio' >$stForm</iframe>\n";
        $stPreviewHtml .= "</div>  \n";
        $stPreviewHtml .= "</body> \n";
        $stPreviewHtml .= "</html> \n";

        if ($this->getFormato() != "pdf") {
            echo $stPreviewHtml;

        } elseif ($this->getDownload() == true) {

            $stHtmlPdf = "<html>";
           $stHtmlPdf .= "<head>";
           $stHtmlPdf .= "<script src=\"../../../../../../gestaoAdministrativa/fontes/javaScript/jquery.js\" type=\"text/javascript\"></script>";
           $stHtmlPdf .= "<script src=\"../../../../../../gestaoAdministrativa/fontes/javaScript/genericas.js\" type=\"text/javascript\"></script>";
           $stHtmlPdf .= "</head>";
           $stHtmlPdf .= "<body onload='LiberaFrames(true,false);'>";
           $stHtmlPdf .= "<iframe src='$stBirtPdfDireto' style=\"border:none;display:none;\" id=\"iframe_birt\" name=\"iframe_birt\" width=\"98%\" height=\"85%\" longdesc=\"IFrame com Relatorio\" ></iframe>\n";
           $stHtmlPdf .= "</body>";
           $stHtmlPdf .= "</html>";
           echo $stHtmlPdf;

        } else {
            echo $stPreviewHtml;
        }

        # DEBUG quando ambiente for desenvolvimento.
        if (constant('ENV_TYPE') == 'dev' && isset($this->stDebug)) {
            if ($this->getDebug() == 'parcial') {
                $this->debug(); // 1 = parcial
            } elseif ($this->getDebug() == 'completo' ) {
                $this->debug($stBirtPdf);
            }
        }

    }

}

?>
