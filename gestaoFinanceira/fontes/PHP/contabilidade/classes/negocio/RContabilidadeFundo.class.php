<?php

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"  );

class RContabilidadeFundo
{
    /**
     * @var Object
     * @access Public
     */
    public $obTransacao;

    /**
     * @access Public
     * @param Object $valor
     */
    public function setTransacao($valor) {
        $this->obTransacao = $valor;
    }

    /**
     * @access Public
     * @return Object $valor
     */
    public function getTransacao() {
        return $this->obTransacao;
    }

    /**
     * @var String
     * @access Public
     */
    public $stExercicio;

    /**
     * @access Public
     * @param String $valor
     */
    public function setExercicio($valor) {
        $this->stExercicio = $valor;
    }

    /**
     * @access Public
     * @return String $valor
     */
    public function getExercicio() {
        return $this->stExercicio;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $inCodFundo;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setCodFundo($valor) {
        $this->inCodFundo = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getCodFundo() {
        return $this->inCodFundo;
    }

    /**
     * @var String
     * @access Public
     */
    public $stCnpj;

    /**
     * @access Public
     * @param String $valor
     */
    public function setCnpj($valor) {
        $this->stCnpj = $valor;
    }

    /**
     * @access Public
     * @return String $valor
     */
    public function getCnpj() {
        return $this->stCnpj;
    }

    /**
     * @var String
     * @access Public
     */
    public $stDescricao;

    /**
     * @access Public
     * @param String $valor
     */
    public function setDescricao($valor) {
        $this->stDescricao = $valor;
    }

    /**
     * @access Public
     * @return String $valor
     */
    public function getDescricao() {
        return $this->stDescricao;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $inCodEntidade;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setCodEntidade($valor) {
        $this->inCodEntidade = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getCodEntidade() {
        return $this->inCodEntidade;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $inCodOrgao;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setCodOrgao($valor) {
        $this->inCodOrgao = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getCodOrgao() {
        return $this->inCodOrgao;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $inCodUnidade;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setCodUnidade($valor) {
        $this->inCodUnidade = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getCodUnidade() {
        return $this->inCodUnidade;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $stContabilidadeCentralizada;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setContabilidadeCentralizada($valor) {
        $this->stContabilidadeCentralizada = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getContabilidadeCentralizada() {
        return $this->stContabilidadeCentralizada;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $stPlano;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setPlano($valor) {
        $this->stPlano = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getPlano() {
        return $this->stPlano;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $stDataExtincao;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setDataExtincao($valor) {
        $this->stDataExtincao = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getDataExtincao() {
        return $this->stDataExtincao;
    }

    /**
     * @var Integer
     * @access Public
     */
    public $stSituacao;

    /**
     * @access Public
     * @param Integer $valor
     */
    public function setSituacao($valor) {
        $this->stSituacao = $valor;
    }

    /**
     * @access Public
     * @return Integer $valor
     */
    public function getSituacao() {
        return $this->stSituacao;
    }

    /**
     * Método Construtor
     * @access Public
     */
    public function RContabilidadeFundo()
    {
        $this->setExercicio( Sessao::getExercicio() );
        $this->setTransacao( new Transacao );

        include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeFundo.class.php";
        $this->obTContabilidadeFundo = new TContabilidadeFundo();
    }

    public function salvar()
    {
        $boFlagTransacao = false;
        $boTransacao = true;

        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $this->obTContabilidadeFundo->setDado("cod_fundo",    $this->inCodFundo);
        $this->obTContabilidadeFundo->setDado("cod_entidade", $this->inCodEntidade);
        $this->obTContabilidadeFundo->setDado("cod_orgao",    $this->inCodOrgao);
        $this->obTContabilidadeFundo->setDado("cod_unidade",  $this->inCodUnidade);

        $this->obTContabilidadeFundo->setDado("exercicio",    $this->stExercicio);
        $this->obTContabilidadeFundo->setDado("cnpj",         $this->stCnpj);
        $this->obTContabilidadeFundo->setDado("situacao",     1);

        $this->obTContabilidadeFundo->setDado("descricao",    $this->stDescricao);
        $this->obTContabilidadeFundo->setDado("plano",        $this->stPlano);
        $this->obTContabilidadeFundo->setDado("contabilidade_centralizada", $this->stContabilidadeCentralizada);

        return $this->obTContabilidadeFundo->inclusao(true);
    }

    public function listar(&$rsRecordSet, $filtrosAdicionais = array(), $stOrder = "", $boTransacao = "")
    {   
        $stFiltro = "WHERE situacao = 1";

        if (isset($filtrosAdicionais['cod_fundo']) && !empty($filtrosAdicionais['cod_fundo'])) {
            $stFiltro .= " AND cod_fundo = " . $filtrosAdicionais['cod_fundo'];
        }

        return $this->obTContabilidadeFundo->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    public function extinguirFundo($exercicio, $cod_fundo, $boTransacao = "")
    {
        return $this->obTContabilidadeFundo->extinguirFundo($exercicio, $cod_fundo, $boTransacao = "");
    }
}
