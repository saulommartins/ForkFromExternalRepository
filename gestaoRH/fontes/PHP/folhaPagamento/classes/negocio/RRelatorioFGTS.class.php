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
    * Classe de regra de relatório para Evento
    * Data de Criação: 11/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Regra de Relatório

    * Casos de uso: uc-04.05.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFGTS.class.php"                                  );
include_once ( CAM_FW_PDF."RRelatorioAgata.class.php"                                               );

class RRelatorioFGTS extends PersistenteRelatorio
{
    /**
        * @var Object
        * @access Private
    */

    public $obRFolhaPagamentoFGTS;

    // variaveis usadas no filtro do relatório
    public $arContratos   = array();  // array de contratos usada para o filtro
    public $arLotacoes    = array();
    public $arLocais      = array(); // array de lotações / locais para o filtro (
    public $stTipoCalculo = '';      // por enquando este parametro é só pra constar pois não será considerado pelo filtro
    public $stOrdenacao   = '';     // A para alfabética por nome N para ordenar por código?????????
    public $stDataFinal   = '';
    public $stCompetencia = '';

    public function setCompetencia($stComp) { $this->stCompetencia = $stComp; }
    public function getCompetencia() { return  $this->stCompetencia;   }

    public function setTipoCalculo($valor) { $this->stTipoCalculo = $valor; }
    public function getTipoCalculo() { return $this->stTipoCalculo;   }

    public function setDataInicio($data) { $this->stDataInicio = $data; }
    public function getDataInicio() { return $this->stDataInicio;  }

    public function setDataFinal($data) { $this->stDataFinal = $data; }
    public function getDataFinal() { return $this->stDataFinal;  }

    public function setOrdenacao($ordem = '') { $this->stOrdenacao = $ordem ; }
    public function getOrdenacao($ordem = '') { return $this->stOrdenacao   ; }

    public function addLotacao($valor) { $this->arLotacoes[] = $valor;  }
    public function limpaLotacao($valor) { $this->arLotacoes[] = array(); }

    public function addLocal($local) { $this->arLocais[] = $local;  }
    public function limpaLocais() { $this->arLocais = array();   }

    public function addContrato($inContrato) { $this->arContratos[] = $inContrato; }
    public function limpaContratos() { $this->arContratos = array();               }

    public function RRelatorioFGTS()
    {
        $this->obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;

    }//function RRelatorioFGTS() {

    public function geraRecordSet(&$rsRecordset)
    {
    }//function geraRecordSet(&$rsRecordset) {

    public function montaFiltro()
    {
        $stFiltro = '';
        $arCondicoes = array();
        $stCondicao = '';

        //// filtro por contratos
        if (count( $this->arContratos ) > 0 ) {
            $stCondicao = ' contrato.registro in ( ' ;
            foreach ($this->arContratos as $inContrato) {
                $stCondicao .= $inContrato .', '; // retirando a ultima virgula que só serve pra dar erro na consulta
            }
            $stCondicao[strlen($stCondicao) - 2 ] = ')';
            $arCondicoes[] = $stCondicao;
        }

        //// filtro por lotações
        if ( count ( $this->arLotacoes) > 0 ) {

            $stCondicao = "vw_orgao_nivel.cod_orgao in (";
            foreach ($this->arLotacoes as $stLotacao) {
                $stCondicao .= "'". $stLotacao ."' , ";
            }
            $stCondicao[strlen($stCondicao) - 2 ] = ')';// retirando a ultima virgula que só serve pra dar erro na consulta
            $arCondicoes[] = $stCondicao;
        }

        //// filtro por locais
        if ( count ( $this->arLocais ) > 0 ) {
            $stCondicao = '';
            $stLocais   = '( ';

            foreach ($this->arLocais as $stLocal) {
               $stLocais .= $stLocal. ', ';
            }
            $stLocais[strlen($stLocais) - 2 ] = ')';

            $arCondicoes[] = "contrato_servidor.cod_contrato in ( select cod_contrato
                               from pessoal.contrato_servidor_local
                               inner join ( select cod_local, max(timestamp)
                                            from pessoal.contrato_servidor_local as lc
                                            where lc.cod_local in $stLocais
                                            group by cod_local ) as max_local
                                   on max_local.cod_local = contrato_servidor_local.cod_local)";
        }

        if ( $this->getCompetencia () ) {
            $arCondicoes[] = "  to_char( periodo_movimentacao.dt_final, 'mm/yyyy') = '".$this->getCompetencia()."'";
        }

        if (count( $arCondicoes )>0 ) {
           $stFiltro = $arCondicoes[0];
           for ( $i = 1 ; $i < count( $arCondicoes ) ; $i++  ) {
                $stFiltro .= ' and '. $arCondicoes[$i];
           }
        }

        return $stFiltro;
    }

    public function geraRelatorio()
    {
        $stFiltro = '';
        $obAgata     = new RRelatorioAgata( CAM_GRH_FOL_AGT."FGTS.agt" );

        $stFiltro = $this->montaFiltro();

        if ($stFiltro) {
            // adicionando filtro a consulta do Agata
            $obAgata->setSQLWhere( $obAgata->getSQLWhere(1).$stFiltro, 1 );
        }

        switch ($this->stOrdenacao) {
            case 'A':
                $obAgata->setSQLOrderBy( ' contrato.cod_contrato  ',1);
            break;
            case 'N':
                $obAgata->setSQLOrderBy( ' sw_cgm.nom_cgm ', 1);
            break;
        }

        $obAgata->Header();

        $ok = $obAgata->generateDocument();
        if (!$ok) {
            echo $obAgata->getError();
        } else {
            $obAgata->fileDialog();
        }
    }//function geraRelatorio() {

}
