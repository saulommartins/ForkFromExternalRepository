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
    * Classe de Regra do relatorio de Reserva de Saldos
    * Data de Criação   : 09/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Author: hwalves $
    $Date: 2007-09-19 18:43:51 -0300 (Qua, 19 Set 2007) $

    * Casos de uso: uc-02.01.08
                    uc-02.01.28
*/

/*
$Log$
Revision 1.20  2007/09/19 21:43:51  hwalves
Ticket#10205#

Revision 1.19  2006/10/25 12:19:07  larocca
Bug #7283#

Revision 1.18  2006/07/24 18:33:24  cako
Bug #6626#

Revision 1.17  2006/07/14 17:58:17  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.16  2006/07/07 14:19:36  cako
Bug #6026#

Revision 1.15  2006/07/06 20:27:54  cako
Bug #6026#

Revision 1.14  2006/07/06 19:30:32  cako
Bug #6026#

Revision 1.13  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO     ."ROrcamentoDespesa.class.php"                      );
include_once ( CLA_PERSISTENTE_RELATORIO        );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );

class ROrcamentoRelatorioReservaSaldos extends PersistenteRelatorio
{
/**
    * @access Private
    * @var Booleam
*/
var $boAnular;
/**
    * @var Object
    * @access Private
*/
var $obTOrcamentoReservaSaldos;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoDespesa;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodReserva;
/**
    * @var String
    * @access Private
*/
var $stDtInicial;
/**
    * @var String
    * @access Private
*/
var $stDtFinal;
/**
    * @var String
    * @access Private
*/
var $stTipo;
/**
    * @var Integer
    * @access Private
*/
var $inNumOrgao;
/**
    * @var Integer
    * @access Private
*/
var $inNumUnidade;
/**
    * @var String
    * @access Private
*/
var $stSituacao;
/**
    * @var String
    * @access Private
*/
var $stListarReservas;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setAnular($valor) { $this->boAnular = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTOrcamentoReservaSaldos($valor) { $this->obTOrcamentoReservaSaldos = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoDespesa($valor) { $this->obROrcamentoDespesa              = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodReserva($valor) { $this->inCodReserva                  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtInicial($valor) { $this->stDtInicial           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtFinal($valor) { $this->stDtFinal             = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipo($valor) { $this->stTipo                   = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumOrgao($valor) { $this->inNumOrgao               = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumUnidade($valor) { $this->inNumUnidade             = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setSituacao($valor) { $this->stSituacao               = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setListarReservas($valor) { $this->stListarReservas         = $valor; }

/**
    * @access Public
    * @return Object
*/
function getAnular() { return $this->boAnular; }
/**
    * @access Public
    * @return Object
*/
function getTOrcamentoReservaSaldos() { return $this->obTOrcamentoReservaSaldos;            }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoDespesa() { return $this->obROrcamentoDespesa;              }
/**
     * @access Public
     * @param String $valor
*/
function getExercicio() { return $this->stExercicio;                    }
/**
     * @access Public
     * @param Integer $valor
*/
function getCodReserva() { return $this->inCodReserva;                   }
/**
     * @access Public
     * @param String $valor
*/
function getDtValidadeInicial() { return $this->stDtValidadeInicial;            }
/**
     * @access Public
     * @param String $valor
*/
function getDtValidadeFinal() { return $this->stDtValidadeFinal;              }
/**
     * @access Public
     * @param String $valor
*/
function getTipo() { return $this->stTipo;                    }
/**
     * @access Public
     * @param Integer $valor
*/
function getNumOrgao() { return $this->inNumOrgao;                }
/**
     * @access Public
     * @param Integer $valor
*/
function getNumUnidade() { return $this->inNumUnidade;              }
/**
     * @access Public
     * @param String $valor
*/
function getSituacao() { return $this->stSituacao;                }
/**
     * @access Public
     * @param String $valor
*/
function getListarReservas() { return $this->stListarReservas;          }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioReservaSaldos()
{
    $this->setROrcamentoDespesa             ( new ROrcamentoDespesa              );
    $this->obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;
    $this->obTransacao                      = new Transacao;
}

/**
    * Lista Reservas para consulta ou anulação de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReservaSaldos(&$rsLista, $stOrder = "cod_reserva", $obTransacao = "")
{
    $stFiltro = "";
    if( $this->inCodReserva )
        $stFiltro .= " cod_reserva = ".$this->inCodReserva." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro .= " cod_despesa = '".$this->obROrcamentoDespesa->getCodDespesa()."' AND ";
    if ($this->stDtInicial && $this->stDtFinal) {
        $stFiltro .= " dt_inclusao BETWEEN TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND TO_DATE('".$this->stDtFinal."', 'dd/mm/yyyy') AND ";
    }
    if( $this->stTipo )
        $stFiltro .= " tipo = '".$this->stTipo."' AND ";
    if( $this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " cod_entidade IN (".$this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro .= " cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." AND ";
    if ($this->boAnular) {
        $stFiltro .= " NOT EXISTS ( SELECT 1                                      \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio = tabela.exercicio     \n";
        $stFiltro .= "            ) AND ";
//         $stFiltro .= " cod_reserva||exercicio not in (select cod_reserva||exercicio from orcamento.reserva_saldos_anulada) AND ";

        $stFiltro .= " dt_anulacao is null AND \n";
        $stFiltro .= "TO_DATE(dt_validade_final,'dd/mm/yyyy') > TO_DATE('".date('d-m-').$this->stExercicio."','dd-mm-yyyy') AND ";
    }
    if( $this->inNumOrgao )
        $stFiltro .= " num_orgao = ".$this->inNumOrgao." AND ";
    if( $this->inNumUnidade )
        $stFiltro .= " num_unidade = ".$this->inNumUnidade." AND ";

    if ($this->stSituacao == 'ativas') {
        $stFiltro .= " situacao = 'Ativa' AND \n";

        $stFiltro .= " NOT EXISTS ( SELECT 1                                      \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio = tabela.exercicio     \n";
        $stFiltro .= "            ) AND ";
//         $stFiltro .= " cod_reserva||exercicio NOT IN ( SELECT cod_reserva||exercicio FROM orcamento.reserva_saldos_anulada ) AND ";

        if ($this->stDtInicial && $this->stDtFinal) {
              $stFiltro .= "( ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') ) ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_final,'dd/mm/yyyy')   >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   >= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') ) ) AND ";
          }
    } elseif ($this->stSituacao == 'inativas') {
        $stFiltro .= " situacao = 'Inativa' AND \n";
        $stFiltro .= " NOT EXISTS ( SELECT 1                                      \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio = tabela.exercicio     \n";
        $stFiltro .= "            ) AND ";
//         $stFiltro .= " cod_reserva||exercicio NOT IN ( SELECT cod_reserva||exercicio FROM orcamento.reserva_saldos_anulada ) AND ";
    } elseif ($this->stSituacao == 'anuladas') {
        $stFiltro .= " situacao = 'Anulada' AND \n";
        $stFiltro .= " cod_reserva||exercicio IN ( SELECT cod_reserva||exercicio FROM orcamento.reserva_saldos_anulada ) AND ";

        if ($this->stDtInicial && $this->stDtFinal) {
              $stFiltro .= "( ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') ) ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_final,'dd/mm/yyyy')   >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   >= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') ) ) AND ";
        }
    }
// Para informar no relatório todas as situações possíveis, tem q trazer todas as reservas do exercício.
// pois serão marcadas como INATIVAS no perído, todas as reservas que já terminaram ou não iniciaram no período informado.
// (cai na condição do mapeamento)

 /*   elseif ($this->stSituacao == "") {
            if ($this->stDtInicial && $this->stDtFinal) {
              $stFiltro .= "( ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') ) ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_final,'dd/mm/yyyy')   >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   <= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= " OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtInicial."', 'dd/mm/yyyy') AND ";
              $stFiltro .= "    TO_DATE(dt_validade_final,'dd/mm/yyyy')   >= TO_DATE('".$this->stDtFinal."',   'dd/mm/yyyy') ) ) AND ";
        }
    } */

    if ($this->stListarReservas == 'manuais') {
        $stFiltro .= " tipo = 'M' AND ";
    } elseif ($this->stListarReservas == 'automaticas') {
        $stFiltro .= " tipo != 'M' AND ";
    }

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $this->obTOrcamentoReservaSaldos->setDado( 'exercicio' , $this->getExercicio() );
    $this->obTOrcamentoReservaSaldos->setDado( 'stFiltro'  , $stFiltro );
    $this->obTOrcamentoReservaSaldos->setDado( 'dataAtual'  , $this->stExercicio.date('-m-d') );

    $this->obTOrcamentoReservaSaldos->setDado( 'stDtInicial', $this->stDtInicial);
    $this->obTOrcamentoReservaSaldos->setDado( 'stDtFinal',   $this->stDtFinal);
    $this->obTOrcamentoReservaSaldos->setDado( 'stSituacao', $this->stSituacao);

    $stOrder = ($stOrder) ? $stOrder : 'cod_reserva';
    $obErro = $this->obTOrcamentoReservaSaldos->recuperaRelacionamento( $rsLista, '', $stOrder, $obTransacao );
//            $this->obTOrcamentoReservaSaldos->debug();
    return $obErro;
}

/**
    * Lista Reservas para consulta ou anulação de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listaRelatorioNotaReserva(&$rsReservaSaldos, $stOrder = "ORS.cod_reserva", $boTransacao = "")
{
    $stFiltro = "";
    if( $this->inCodReserva )
        $stFiltro .= " ORS.cod_reserva = ".$this->inCodReserva." AND ";
    if( $this->stExercicio )
        $stFiltro .= " ORS.exercicio = '".$this->stExercicio."' AND ";
    $stFiltro = ($stFiltro) ? ' AND '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? ' ORDER BY '.$stOrder : ' ORDER BY ORS.cod_reserva';

    $obErro = $this->obTOrcamentoReservaSaldos->recuperaRelatorioNotaReserva( $rsReservaSaldos, $stFiltro, $stOrder, $boTransacao );
    $arRecordSet = array();
    if ( !$obErro->ocorreu() and !$rsReservaSaldos->eof() ) {
        $inCount = 0;
        $arRecordSet[$inCount]['descricao'] = 'Número da Reserva';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'cod_reserva' ).'/'.$rsReservaSaldos->getCampo( 'exercicio' );
        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Entidade';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'cod_entidade' ).' - '.$rsReservaSaldos->getCampo( 'nom_cgm' );
        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Dotação Orçamentária';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'cod_despesa' ).' - '.$rsReservaSaldos->getCampo( 'nom_conta' );
        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Órgão / Unidade';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'num_orgao' ).'/'.$rsReservaSaldos->getCampo( 'nom_orgao' );
        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Unidade Orçamentária';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'num_unidade' ).'/'.$rsReservaSaldos->getCampo( 'nom_unidade' );
        $inCount++;

        $stMotivoTemp = str_replace( chr(10), "", $rsReservaSaldos->getCampo('motivo') );
        $stMotivoTemp = wordwrap( $stMotivoTemp,80,chr(13) );
        $arMotivoOLD = explode( chr(13), $stMotivoTemp );
            $inCountQuebra = $inCount;

        $arRecordSet[$inCount]['descricao'] = 'Motivo da Reserva';
        foreach ($arMotivoOLD as $stMotivoTemp) {
            $arRecordSet[$inCountQuebra]['valor'] = $stMotivoTemp;
            $inCountQuebra++;
        }
        $inCount = $inCountQuebra;

        $arRecordSet[$inCount]['descricao'] = 'Valor R$';
        $arRecordSet[$inCount]['valor'    ] = number_format($rsReservaSaldos->getCampo( 'vl_reserva' ), 2, ',', '.');

        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Usuário que Incluiu';
        $arRecordSet[$inCount]['valor'    ] = Sessao::read('nomCgm');
        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Reserva efetuada em';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'dt_inclusao' );
        $inCount++;
        $arRecordSet[$inCount]['descricao'] = 'Data de Validade';
        $arRecordSet[$inCount]['valor'    ] = $rsReservaSaldos->getCampo( 'dt_validade_final' );
    }
    $rsReservaSaldos = new RecordSet;
    $rsReservaSaldos->preenche( $arRecordSet );

    return $obErro;
}

}
