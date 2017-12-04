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
    * Classe de Regra de Negócio Configuracão FISCALIZACAO
    * Data de Criação   : 06/06/2008

    * @author Analista : Heleno Santos
    * @author Desenvolvedor: Janio Eduardo
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO . "RConfiguracaoConfiguracao.class.php" );
include_once( CAM_GT_MON_MAPEAMENTO."TMONIndicadorEconomico.class.php" );
include_once( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );

class RFISConfiguracao extends RConfiguracaoConfiguracao
{
    public $stExercicio;
    public $inCodModulo;
    public $inNormaInicio;
    public $inNormaTermino;
    public $inIndicadorEconomico;
    public $inDocumentoAutoInfracao;

    public function getCodModulo()
    {
        return $this->inCodModulo;
    }

    public function getExercicio()
    {
        return $this->stExercicio;
    }

    public function getNormaInicio()
    {
        return $this->inNormaInicio;
    }

    public function getNormaTermino()
    {
        return $this->inNormaTermino;
    }

    public function getDocumentoAutoInfracao()
    {
        return $this->inDocumentoAutoInfracao;
    }

    public function getIndicadorEconomico()
    {
        return $this->inIndicadorEconomico;
    }

    public function setExercicio($valor)
    {
        $this->stExercicio = $valor;
    }

    public function setNormaInicio($valor)
    {
        $this->inNormaInicio = $valor;
    }

    public function setNormaTermino($valor)
    {
        $this->inNormaTermino = $valor;
    }

    public function setDocumentoAutoInfracao($valor)
    {
        $this->inDocumentoAutoInfracao = $valor;
    }

    public function setIndicadorEconomico($valor)
    {
        $this->inIndicadorEconomico = $valor;
    }

    public function RFISConfiguracao()
    {
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
        include_once( CLA_TRANSACAO );
        parent::RConfiguracaoConfiguracao();
        $this->obTConfiguracao  = new TAdministracaoConfiguracao;
        $this->obTransacao      = new Transacao;
        $this->setExercicio     ( Sessao::getExercicio() );
        $this->setCodModulo     ( 34 );
    }

    public function consultar($boTransacao = "")
    {
            $this->obTConfiguracao->setDado( "cod_modulo", $this->getCodModulo() );
               $this->obTConfiguracao->setDado( "exercicio" , $this->getExercicio() );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTConfiguracao->setDado( "parametro" , 'norma_inicio' );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setNormaInicio( $rsConfiguracao->getCampo( "valor" ) );

            if ( !$obErro->ocorreu() ) {
                $this->obTConfiguracao->setDado( "parametro" , 'norma_termino' );
                $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                $this->setNormaTermino( $rsConfiguracao->getCampo( "valor" ) );
            }

            if ( !$obErro->ocorreu() ) {
                $this->obTConfiguracao->setDado( "parametro" , 'nom_modelo' );
                $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
                $this->setDocumentoAutoInfracao( $rsConfiguracao->getCampo( "valor" ) );
            }

            $this->obTConfiguracao->setDado( "parametro" , 'fis_indice_correcao' );
            $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
            $this->setIndicadorEconomico( $rsConfiguracao->getCampo( "valor" ) );
        }
    }

    public function configurar($param, $boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            /* inicio norma */
            $this->setParametro ("norma_inicio");
            $this->setValor     ($param['norma_inicio']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* termino norma */
            $this->setParametro ( "norma_termino" );
            $this->setValor     ( $param['norma_termino']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* documento auto infração */
            $this->setParametro ( "nom_modelo" );
            $this->setValor     ( $param['stCodDocumento']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }

            /* indicador econômico */
            $this->setParametro ( "fis_indice_correcao" );
            $this->setValor     ( $param['inCodIndicador']);
            $this->verificaParametro( $boExiste, $boTransacao );

            if ($boExiste) {
                parent::alterar( $boTransacao );
            } else {
                parent::incluir( $boTransacao );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTConfiguracao );

        if (!$obErro->ocorreu()) {
            sistemaLegado::exibeAviso( "Efetuada com sucesso", "incluir", "aviso" );
        } else {
            sistemaLegado::exibeAviso( "Erro ao atualizar","n_incluir","erro" );
        }

//		sistemaLegado::exibeAviso( "Efetuada com sucesso", "incluir", "aviso" );
    }

    public function descricao($codNorma)
    {
        $sql = " where cod_norma =".$codNorma;
        $obTNorma = new TNorma();
        $obRSNorma = new recordSet();
        $obTNorma->recuperaTodos($obRSNorma,$sql);
        $descricaoNorma = $obRSNorma->getCampo('nom_norma');
        #retirando dt_publicação não previsto na interface abstrata
        //$descricaoNorma = $obRSNorma->getCampo('dt_publicacao')." - ".$obRSNorma->getCampo('nom_norma');
        return $descricaoNorma;
    }

    public function nomeIndicador($codIndicador)
    {
        $obTMONIndicador = new TMONIndicadorEconomico;
        $stFiltro = " WHERE cod_indicador = ". $codIndicador." \n";
        $obTMONIndicador->recuperaTodos( $rsIndicador, $stFiltro, " ORDER BY cod_indicador " );

        $stDescricao = $rsIndicador->getCampo('descricao');

        return $stDescricao;
    }
} // fecha classe

?>
