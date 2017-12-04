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
    * Página de Formulario de Inclusao/Alteracao de Documentos

    * Data de Criação   : 25-07-2008

    * @author Analista      : Heleno Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id: FMManterFiscal.php 30621 2008-07-24 12:04:24Z cercato $

    *Casos de uso: uc-05.07.04
*/
include_once CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'TFISTipoFiscalizacao.class.php';

$CriterioSql = null;

$stPrograma = "ManterDocumento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

class RFISDocumento
{
    public function __construct()
    {
        $this->obTFISDocumento = new TFISDocumento;

    }

    public function Todos($criterio)
    {
        $where = " where ";

        if($criterio)
            $criterio = $where.$criterio;

        $obRsDocumento = new RecordSet();

        $this->obTFISDocumento->recuperaTodos($obRsDocumento,$criterio);

        return $obRsDocumento;
    }

    public function setCriterio($vlr)
    {
        $this->CriterioSql = $vlr;
    }

    public function getDocumento($condicao)
    {
        return $this->Todos($condicao);
    }

    public function incluirDocumento($param)
    {
        global $pgForm;

        $arValores = Sessao::read( 'arValores' );

        $inCodDocumento = null;

        $this->obTFISDocumento->proximoCod($inCodDocumento);
        $rsRecordSetFISDocumento   = new RecordSet();

        // inclusão dos dados
        $this->obTFISDocumento->setDado( "cod_documento", $inCodDocumento      );
        $this->obTFISDocumento->setDado( "cod_tipo_fiscalizacao", $param['cmbTipoFiscalizacao']           );
        $this->obTFISDocumento->setDado( "nom_documento",  $param['nom_documento']    );
        $this->obTFISDocumento->setDado( "uso_interno",( $param['boUsoInterno']=="UsoInterno" ) ? "true" : "false");
        $this->obTFISDocumento->setDado( "ativo"       , ( $param['boAtivo']=="sim" ) ? "true" : "false" );
        $this->obTFISDocumento->inclusao();

        return sistemaLegado::alertaAviso($pgForm , $inCodDocumento ,"incluir","aviso", Sessao::getId(), "../");
    }

    public function excluirDocumento($param)
    {
           global $pgList;

        # Inicia nova transação
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $this->obTFISDocumento->setDado( "cod_documento", $param["cod_documento"] );
        $obErro = $this->obTFISDocumento->exclusao($boTransacao );
        $stCaminho = $pgList."?".Sessao::getId()."&stAcao=excluir";

        if ($obErro->ocorreu()) {
            sistemaLegado::alertaAviso($stCaminho,"Documento vinculado com processo fiscal ou vinculado com atividades","n_excluir","erro",Sessao::getId(), "../");
        }

        # Termina transação
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFISDocumento );
        sistemaLegado::alertaAviso($stCaminho,$param['cod_documento'] ,"excluir","aviso",Sessao::getId(),"../");
    }

    public function alterarDocumento($param)
    {
        global $pgList;

        $rsRecordSetDocumento     = new RecordSet();
        $this->obTFISDocumento->setDado( "cod_documento"  , $param['cod_documento']     );
        $this->obTFISDocumento->setDado( "cod_tipo_fiscalizacao"      , $param['cod_tipo_fiscalizacao']         );
        $this->obTFISDocumento->setDado( "nom_documento"      , $param['nom_documento']     		     );
        $this->obTFISDocumento->setDado( "uso_interno",( $param['boUsoInterno']=="UsoInterno" ) ? "true" : "false"        );
        $this->obTFISDocumento->setDado( "ativo"       ,( $param['boAtivo']=="sim" ) ? "true" : "false" 	     );

        $this->obTFISDocumento->alteracao();

        return SistemaLegado::alertaAviso($pgList , $param['cod_documento'] ,"alterar","aviso", Sessao::getId(), "../");
    }

    public function listarDocumento($param)
    {
        //pega valor do FL para tipo de fiscalizacao
        if (isset($param['cmbTipoFiscalizacao'])) {
            $stFiltro = " AND documento.cod_tipo_fiscalizacao= ".$param['cmbTipoFiscalizacao']."
                          AND documento.nom_documento ILIKE '%".$param['nom_documento']."%'\n";
        }

        $this->obTFISDocumento->recuperaDocumento($obRsDocumento, $stFiltro);

        return 	$obRsDocumento;
    }

    public function listarDocumentoAlterar($param)
    {
        //pega valor do FL para tipo de fiscalizacao
        if (isset($param['cmbTipoFiscalizacao'])) {
            $stFiltro = " AND documento.cod_tipo_fiscalizacao = ".$param['cmbTipoFiscalizacao']." \n";
        }

        if (isset($param['cod_documento'])) {
            $stFiltro = " AND documento.cod_documento = ".$param['cod_documento']." \n";
        }

        $this->obTFISDocumento->recuperaDocumento($obRsDocumento, $stFiltro);

        return 	$obRsDocumento;
    }
}
?>
