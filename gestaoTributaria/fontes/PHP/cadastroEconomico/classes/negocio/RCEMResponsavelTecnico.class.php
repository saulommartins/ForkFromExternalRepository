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
    * Classe de regra de negócio para Responsavel Tecnico
    * Data de Criação: 14/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMResponsavelTecnico.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.12  2007/04/16 14:08:21  cassiano
Bug #8427#

Revision 1.11  2007/03/27 19:29:07  rodrigo
Bug #8768#

Revision 1.10  2007/02/22 14:43:15  rodrigo
Bug #8426#

Revision 1.9  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMResponsavelTecnico.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMResponsavel.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMResponsavelEmpresa.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMEmpresaProfissao.class.php" );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );

/**
    * Classe de regra de negócio Responsavel Tecnico
    * Data de Criação: 14/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Stephanou
    * @package URBEM
    * @subpackage Regra
*/

class RCEMResponsavelTecnico
{
    /**
    * @access   Private
    * @type     Array
    */
    public $arProfissoes;

    /**
    * @access   Private
    * @type     Array
    */
    public $arResponsaveis;

    /**
    * @access   Private
    * @type     Integer
    */
    public $inNumCgmResponsavel;

    /**
    * @access   Private
    * @type     Integer
    */
    public $inNumCgm;

    /**
    * @access   Private
    * @type     String
    */
    public $stNomCgm;

    /**
    * @access   Private
    * @type     String
    */
    public $inCodigoProfissao;

    /**
    * @access   Private
    * @type     String
    */
    public $stNumRegistro;

    /**
    * @access   Private
    * @type     Integer
    */
    public $inCodigoUF;

    /**
    * @access   Private
    * @type     Integer
    */
    public $inSequenciaResponsavel;

    /**
    * @access   Private
    * @type     Integer
    */
    public $inSequencia;

    /**
    * @access   Private
    * @type     Object
    */
    public $obTCEMEmpresaProfissao;

    /**
    * @access   Private
    * @type     Object
    */
    public $obTCEMResponsavel;

    /**
    * @access   Private
    * @type     Object
    */
    public $obTCEMResponsavelTecnico;

    /**
    * @access   Private
    * @type     Object
    */
    public $obTCEMResponsavelEmpresa;

    /**
    * @access   Private
    * @type     Object
    */
    public $roRCEMInscricao;

    /**
    * @access Public
    * @param Array $valor
    */
    public function setProfissoes($valor) { $this->arProfissoes = $valor; }

    /**
    * @access Public
    * @param Array $valor
    */
    public function setResponsaveis($valor) { $this->arResponsaveis = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setSequenciaResponsavel($valor) { $this->inSequenciaResponsavel = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setSequencia($valor) { $this->inSequencia = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setNumCgmResponsavel($valor) { $this->inNumCgmResponsavel = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setNumCgm($valor) { $this->inNumCgm = $valor; }

    /**
    * @access Public
    * @param Integer $valor
    */
    public function setNomCgm($valor) { $this->stNomCgm = $valor; }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoProfissao($valor) { $this->inCodigoProfissao = $valor; }

    /**
    * @access Public
    * @param String $valor
    */
    public function setNumRegistro($valor) { $this->stNumRegistro = $valor; }

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodigoUF($valor) { $this->inCodigoUF = $valor; }

    /**
        * @access Public
        * @param Object $valor
    */
    public function setTCEMEmpresaProfissao($valor) {$this->obTCEMEmpresaProfissao = $valor; }

    /**
        * @access Public
        * @param Object $valor
    */
    public function setTCEMResponsavelTecnico($valor) { $this->obResponsavelTecnico = $valor; }

    //GETTERS
    /**
    * @access Public
    * @return Array $valor
    */
    public function getProfissoes() { return $this->arProfissoes; }

    /**
    * @access Public
    * @return Array $valor
    */
    public function getResponsaveis() { return $this->arResponsaveis; }

    /**
    * @access Public
    * @return Integer $valor
    */
    public function getSequenciaResponsavel() { return $this->inSequenciaResponsavel; }

    /**
        * @access Public
        * @return Integer $valor
    */
    public function getSequencia() { return $this->inSequencia; }

    /**
        * @access Public
        * @return Integer $valor
    */
    public function getNumCgmResponsavel() { return $this->inNumCgmResponsavel; }

    /**
        * @access Public
        * @return Integer $valor
    */
    public function getNumCgm() { return $this->inNumCgm; }

    /**
        * @access Public
        * @return Integer $valor
    */
    public function getNomCgm() { return $this->stNomCgm; }

    /**
        * @access Public
        * @return Integer $valor
    */
    public function getCodigoProfissao() { return $this->inCodigoProfissao; }

    /**
        * @access Public
        * @return String  $valor
    */
    public function getNumRegistro() { return $this->stNumRegistro; }

    /**
        * @ACCEss Public
        * @return Integer $valor
    */
    public function getCodigoUF() { return $this->inCodigoUF; }

    /**
        * @access Public
        * @return Object $valor
    */
    public function getTCEMEmpresaProfissao() { return $this->obTCEMEmpresaProfissao; }

    /**
        * @access Public
        * @return Object $valor
    */
    public function getTCEMResponsavelTecnico() { return $this->obTCEMResponsavelTecnico; }

    /**
        * Metodo de Construtor
        * @access Public
        * @return 0
    */
    public function RCEMResponsavelTecnico()
    {
        $this->obTransacao = new Transacao;
        $this->obTCEMEmpresaProfissao = new TCEMEmpresaProfissao;
        $this->obTCEMResponsavel = new TCEMResponsavel;
        $this->obTCEMResponsavelEmpresa = new TCEMResponsavelEmpresa;
        $this->obTCEMResponsavelTecnico = new TCEMResponsavelTecnico;
        $this->obRProfissao = new RProfissao;
        $this->inRegistro = 0;
        $this->inCodigoUF = "";
    }

    /**
        * Metodo de Inclusao Responsavel Empresa
        * @access Public
        * @return $obErro boolean
    */
    public function incluirResponsavelEmpresa($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obTransacao = new Transacao;

        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMResponsavel->setDado( "numcgm", $this->getNumCgm() );
            $stFiltro = "WHERE numcgm=".$this->getNumCgm();
            $this->obTCEMResponsavel->recuperaSequencia( $rsResponsavel, $stFiltro );
            if ( $rsResponsavel->getCampo("max_sequencia") == "" ) { //nao existia responsavel na tabela responsavel
                $sequencia = 1;
            } else {
                $sequencia = $rsResponsavel->getCampo("max_sequencia");
                $sequencia++;
            }

            $this->obTCEMResponsavel->setDado( "sequencia", $sequencia );
            $obErro = $this->obTCEMResponsavel->inclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMResponsavelEmpresa->setDado( "sequencia", $sequencia );
                $this->obTCEMResponsavelEmpresa->setDado( "numcgm", $this->getNumCgm() );

                $inCount = count( $this->arResponsaveis );
                $cont = 0;
                while ($cont < $inCount) {
                    $this->obTCEMResponsavelEmpresa->setDado( "numcgm_resp_tecnico", $this->arResponsaveis[$cont]['num_cgm'] );
                    $this->obTCEMResponsavelEmpresa->setDado( "sequencia_resp_tecnico", $this->arResponsaveis[$cont]['sequencia'] );

                    $obErro = $this->obTCEMResponsavelEmpresa->inclusao( $boTransacao );
                    if ( $obErro->ocorreu )
                        return $obErro;
                    else
                        $cont++;
                }

                $this->obTCEMEmpresaProfissao->setDado( "numcgm", $this->getNumCgm() );
                $this->obTCEMEmpresaProfissao->setDado( "sequencia", $sequencia );
                $inCount = count( $this->arProfissoes );
                $cont = 0;
                while ($cont < $inCount) {
                    $this->obTCEMEmpresaProfissao->setDado( "cod_profissao", $this->arProfissoes[$cont]['cod_profissao'] );

                    $obErro = $this->obTCEMEmpresaProfissao->inclusao ( $boTransacao );
                    if ( $obErro->ocorreu )
                        return $obErro;
                    else
                        $cont++;
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMEmpresaProfissao );

        return $obErro;
    }

    /**
        * Metodo de Alteracao Responsavel Empresa
        * @access Public
        * @return $obErro boolean
    */
    public function alterarResponsavelEmpresa($boTransacao = "")
    {
        $this->obTCEMResponsavelEmpresa->setDado( "numcgm", $this->getNumCgm() );
        $this->obTCEMResponsavelEmpresa->setDado( "sequencia", $this->getSequencia() );
        $this->obTCEMResponsavelEmpresa->setDado( "numcgm_resp_tecnico", $this->getNumCgmResponsavel() );
        $this->obTCEMResponsavelEmpresa->setDado( "sequencia_resp_tecnico", $this->getSequenciaResponsavel() );

        $obErro = $this->obTCEMResponsavelEmpresa->alteracao( $boTransacao );

        return $obErro;
    }

    /**
        * Metodo de Exclusao de Responsavel Empresa
        * @access Public
        * @return $obErro boolean
    */
    public function excluirResponsavelEmpresa($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obTransacao = new Transacao;

        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMResponsavel->setDado( "numcgm", $this->getNumCgm() );
            $this->obTCEMResponsavel->setDado( "sequencia", $this->getSequencia() );
            $obErro = $this->obTCEMResponsavel->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMResponsavelEmpresa->setDado( "sequencia", $this->getSequencia() );
                $this->obTCEMResponsavelEmpresa->setDado( "numcgm", $this->getNumCgm() );
                $this->obTCEMResponsavelEmpresa->setDado( "numcgm_resp_tecnico", $this->getNumCgmResponsavel() );
                $this->obTCEMResponsavelEmpresa->setDado( "sequencia_resp_tecnico", $this->getSequenciaResponsavel() );

                $obErro = $this->obTCEMResponsavelEmpresa->exclusao( $boTransacao );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMResponsavelEmpresa );

        return $obErro;
    }

    /**
        * Metodo de Inclusao Responsavel Tecnico
        * @access Public
        * @return $obErro boolean
    */
    public function incluirResponsavelTecnico($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obTransacao = new Transacao;

        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMResponsavel->setDado( "numcgm", $this->getNumCgm() );
            $stFiltro = "WHERE numcgm=".$this->getNumCgm();
            $this->obTCEMResponsavel->recuperaSequencia( $rsResponsavel, $stFiltro );
            if ( $rsResponsavel->getCampo("max_sequencia") == "" ) { //nao existia responsavel na tabela responsavel
                $sequencia = 1;
            } else {
                $sequencia = $rsResponsavel->getCampo("max_sequencia");
                $sequencia++;
            }

            $this->obTCEMResponsavel->setDado( "sequencia", $sequencia );
            $obErro = $this->obTCEMResponsavel->inclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMResponsavelTecnico->setDado( "sequencia", $sequencia );
                $this->obTCEMResponsavelTecnico->setDado( "numcgm", $this->getNumCgm() );
                $this->obTCEMResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
                $this->obTCEMResponsavelTecnico->setDado( "num_registro", $this->getNumRegistro() );
                $this->obTCEMResponsavelTecnico->setDado( "cod_uf", $this->getCodigoUF() );

                $obErro = $this->obTCEMResponsavelTecnico->inclusao( $boTransacao );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMResponsavelTecnico );

        return $obErro;
    }

    /**
        * Metodo de Alteracao Responsavel Tecnico
        * @access Public
        * @return $obErro boolean
    */
    public function alterarResponsavelTecnico($boTransacao = "")
    {
        $this->obTCEMResponsavelTecnico->setDado( "numcgm", $this->getNumCgm() );
        $this->obTCEMResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );
        $this->obTCEMResponsavelTecnico->setDado( "num_registro", $this->getNumRegistro() );
        $this->obTCEMResponsavelTecnico->setDado( "cod_uf", $this->getCodigoUF() );
        $this->obTCEMResponsavelTecnico->setDado( "sequencia", $this->getSequencia() );

        $obErro = $this->obTCEMResponsavelTecnico->alteracao( $boTransacao );

        return $obErro;
    }

    /**
        * Metodo de Exclusao de Responsavel Tecnico
        * @access Public
        * @return $obErro boolean
    */
    public function excluirResponsavelTecnico($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obTransacao = new Transacao;

        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $tmp_num_cgm = $this->getNumCgm();
            $this->setNumCgm( "" );

            $this->setNumCgmResponsavel( $tmp_num_cgm );
            $this->listarResponsavelEmpresa( $rsListaResponsavelEmpresa );

            $this->setNumCgm( $tmp_num_cgm );
            $this->setNumCgmResponsavel( "" );

            if ( !$rsListaResponsavelEmpresa->eof() ) {
                $obErro->setDescricao ('Responsavel técnico está sendo utilizado como responsavel empresa! ('. $this->getNumCgm() .')');
            } else {
                $this->obTCEMResponsavel->setDado( "numcgm", $this->getNumCgm() );
                $this->obTCEMResponsavel->setDado( "sequencia", $this->getSequencia() );
                $obErro = $this->obTCEMResponsavel->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCEMResponsavelTecnico->setDado( "sequencia", $this->getSequencia() );
                    $this->obTCEMResponsavelTecnico->setDado( "numcgm", $this->getNumCgm() );
                    $this->obTCEMResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );

                    $obErro = $this->obTCEMResponsavelTecnico->exclusao( $boTransacao );
                }
                if ( $obErro->ocorreu() AND strpos($obErro->getDescricao(), "fk_") ) {
                    GLOBAL $_REQUEST;
                    $obErro->setDescricao("O(A) responsável ".$_REQUEST['stDescQuestao']." não pode ser excluído(a) porque está sendo utilizada pelo sistema!");
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMResponsavelTecnico );

        return $obErro;
    }

    /**
        * Metodo para consultar Responsavel Tecnico
        * @access Public
        * @return $obErro boolean
    */
    public function consultarResponsavelTecnico($boTransacao = "")
    {
        $this->obTCEMResponsavelTecnico->setDado( "numcgm", $this->getNumCgm() );
        $this->obTCEMResponsavelTecnico->setDado( "cod_profissao", $this->getCodigoProfissao() );

        $stFiltro = " WHERE true ";
        if ($this->getNumCgm()) {
            $stFiltro .= " AND numcgm = ".$this->getNumCgm()." ";
        }
        if ($this->getCodigoProfissao()) {
            $stFiltro .= " AND cod_profissao = ".$this->getCodigoProfissao()." ";
        }

        $obErro = $this->obTCEMResponsavelTecnico->recuperaTodos( $rsListaResponsavelTecnico, $stFiltro, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setNumRegistro   ( $rsListaResponsavelTecnico->getCampo( "num_registro"    ) );
            $this->setCodigoUF      ( $rsListaResponsavelTecnico->getCampo( "cod_uf"          ) );
            $this->setSequencia     ( $rsListaResponsavelTecnico->getCampo( "sequencia"       ) );
        }

        return $obErro;
    }

    /**
        * Retorna recordet preenchido com todos os responsaveis empresa
        * @access Public
        * @return $obErro boolean
    */
    public function listarResponsavelEmpresa(&$rsListaResponsavelTecnico, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->obRProfissao->getCodigoProfissao() ) {
            $stFiltro .= " cod_profissao = ".$this->obRProfissao->getCodigoProfissao()." AND";
        }

        if ( $this->getNumCgm() ) {
            $stFiltro .= " numcgm = ".$this->getNumCgm()." AND ";
        }

        if ( $this->getNumCgmResponsavel() ) {
            $stFiltro .= " numcgm_resp_tecnico = ".$this->getNumCgmResponsavel()." AND ";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE \r\n\t ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }

        $obErro = $this->obTCEMResponsavelEmpresa->recuperaTodos( $rsListaResponsavelTecnico ,$stFiltro );

        return $obErro;
    }

    /**
        * Retorna recordet preenchido com todos os responsaveis tecnicos em geral
        * @access Public
        * @return $obErro boolean
    */
    public function listarTecnico(&$rsListaResponsavelTecnico, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getNumCgm() ) {
            $stFiltro.= " AND sw_cgm.numcgm = ".$this->getNumCgm()." \n";
        }
        $stOrdem = "";
        $obErro = $this->obTCEMResponsavelTecnico->recuperaTecnico( $rsListaResponsavelTecnico ,$stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
        * Retorna recordet preenchido com todos os responsaveis tecnicos
        * @access Public
        * @return $obErro boolean
    */
    public function listarResponsavelTecnico(&$rsListaResponsavelTecnico, $boTransacao = "")
    {
        $stFiltro = "";

        if ($this->arProfissoes) {
            $arAtividades = explode (',', $this->arProfissoes );
            $cont = 0;
            while ($cont < count ($arAtividades)) {
                if ($arAtividades[$cont] != '') {
                    $stFiltro .= " cod_profissao = ". $arAtividades[$cont] ." OR ";
                }
                $cont++;
            }

            $stFiltro = " ( ".substr( $stFiltro, 0, strlen( $stFiltro ) - 3 )." ) AND ";
        }

        if ( $this->obRProfissao->getCodigoProfissao() ) {
            $stFiltro .= " cod_profissao = ".$this->obRProfissao->getCodigoProfissao()." AND";
        }
        if ( $this->getNumCgm() ) {
            $stFiltro .= " numcgm = ".$this->getNumCgm()." AND ";
        }
        if ( $this->getNomCgm() ) {
            $stFiltro .= " nom_cgm like '%".$this->getNomCgm()."%' AND ";
        }
        if ( $this->getNumRegistro() ) {
            $stFiltro .= " num_registro = '".$this->getNumRegistro()."' AND";
        }
        if ( $this->getCodigoUF() ) {
            $stFiltro .= " cod_uf = ".$this->getCodigoUF()." AND ";
        }

        if ($stFiltro) {
            $stFiltro = "WHERE \r\n\t ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrdem = " ORDER BY nom_cgm, numcgm ";
        $obErro = $this->obTCEMResponsavelTecnico->recuperaRelacionamento( $rsListaResponsavelTecnico ,$stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
        * Retorna recordet preenchido com as profissoes
        * @access Public
        * @return $obErro boolean
    */
    public function listarProfissoes(&$rsListaProfissoes, $boTransacao = "")
    {
        $stFiltro = "";
        if ($this->arProfissoes) {
            $arAtividades = explode (',', $this->arProfissoes );
            $cont = 0;
            while ($cont < count ($arAtividades)) {
                if ($arAtividades[$cont] != '') {
                    $stFiltro .= " cod_profissao = ". $arAtividades[$cont] ." OR ";
                }
                $cont++;
            }

            $stFiltro = " ".substr( $stFiltro, 0, strlen( $stFiltro ) - 3 );
        }

        if ($stFiltro) {
            $stFiltro = "WHERE \r\n\t ".$stFiltro;
        }

        $stOrdem = "";
        $obErro = $this->obTCEMResponsavelTecnico->recuperaProfissoes( $rsListaProfissoes ,$stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
        * Verifica se já não existe um registro para o mesmo UF
        * @access Public
        * @return $obErro boolean
    */
    public function verificaResponsavelTecnico(&$rsListaResponsavelTecnico, $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->getNumRegistro() ) {
            $stFiltro .= " num_registro = '".$this->getNumRegistro()."' AND";
        }
        if ( $this->getCodigoUF() ) {
            $stFiltro .= " cod_uf = ".$this->getCodigoUF()." AND ";
        }

        if ( $this->getCodigoProfissao() ) {
            $stFiltro .= " cod_profissao = ".$this->getCodigoProfissao()." AND ";
        }
        if ( $this->getNumCgm() ) {
            $stFiltro .= " numcgm = ".$this->getNumCgm()." AND";
        }
        if ($stFiltro) {
            $stFiltro = "WHERE \r\n\t ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        }
        $stOrdem = " ORDER BY numcgm ";
        $obErro = $this->obTCEMResponsavelTecnico->recuperaRelacionamento( $rsListaResponsavelTecnico ,$stFiltro ,$stOrdem ,$boTransacao );

        return $obErro;
    }

    /**
        * Faz a referencia com um objeto de Inscrição Ecômica
        * @access Public
        * @param Objet objeto de Inscricao Economica
    */
    public function addInscricao(&$RCEMInscricaoEconomica)
    {
        $this->roRCEMInscricao = &$RCEMInscricaoEconomica;
    }
}
?>
