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
    * Classe de Regra de Negócio de Reserva Saldos
    * Data de Criação   : 28/04/2005

    * @author Analista : Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-02-29 09:02:14 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-02.01.08
                    uc-02.01.23
                    uc-02.03.02
                    uc-02.01.28
*/

/*
$Log$
Revision 1.14  2006/07/24 18:33:24  cako
Bug #6626#

Revision 1.13  2006/07/14 17:58:17  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.12  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO     ."ROrcamentoDespesa.class.php"                      );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                   );

class ROrcamentoReservaSaldos
{
/**
    * @access Private
    * @var Booleam
*/
var $boAnular;
/**
    * @access Private
    * @var Object
*/
var $obREmpenhoEmpenho;
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
var $stDtValidadeInicial;
/**
    * @var String
    * @access Private
*/
var $stDtValidadeFinal;
/**
    * @var String
    * @access Private
*/
var $stDtInclusao;
/**
    * @var Numeric
    * @access Private
*/
var $nuVlReserva;
/**
    * @var String
    * @access Private
*/
var $stTipo;
/**
    * @var String
    * @access Private
*/
var $stSituacao;
/**
    * @var String
    * @access Private
*/
var $stMotivo;
/**
    * @var String
    * @access Private
*/
var $stDtAnulacao;
/**
    * @var String
    * @access Private
*/
var $stMotivoAnulacao;
/**
    * @access Public
    * @param Boolean $Valor
*/
function setAnular($valor) { $this->boAnular = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setREmpenhoEmpenho($valor) { $this->obREmpenhoEmpenho = $valor; }
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
function setDtValidadeInicial($valor) { $this->stDtValidadeInicial           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtValidadeFinal($valor) { $this->stDtValidadeFinal             = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtInclusao($valor) { $this->stDtInclusao                  = $valor; }
/**
     * @access Public
     * @param Numeric $valor
*/
function setVlReserva($valor) { $this->nuVlReserva                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setTipo($valor) { $this->stTipo                       = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setSituacao($valor) { $this->stSituacao                   = $valor; }

/**
     * @access Public
     * @param String $valor
*/
function setMotivo($valor) { $this->stMotivo                   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDtAnulacao($valor) { $this->stDtAnulacao                  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMotivoAnulacao($valor) { $this->stMotivoAnulacao                  = $valor; }

/**
    * @access Public
    * @return Object
*/
function getAnular() { return $this->boAnular; }
/**
    * @access Public
    * @return Object
*/
function getREmpenhoEmpenho() { return $this->obREmpenhoEmpenho; }
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
function getDtInclusao() { return $this->stDtInclusao;                   }
/**
     * @access Public
     * @param Numeric $valor
*/
function getVlReserva() { return $this->nuVlReserva;                     }
/**
     * @access Public
     * @param String $valor
*/
function getTipo() { return $this->stTipo;                           }
/**
     * @access Public
     * @param String $valor
*/
function getSituacao() { return $this->stSituacao;                       }

/**
     * @access Public
     * @param String $valor
*/
function getMotivo() { return $this->stMotivo;                    }
/**
     * @access Public
     * @param String $valor
*/
function getDtAnulacao() { return $this->stDtAnulacao;                   }
/**
     * @access Public
     * @param String $valor
*/
function getMotivoAnulacao() { return $this->stMotivoAnulacao;                   }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoReservaSaldos()
{
    $this->setROrcamentoDespesa             ( new ROrcamentoDespesa              );
    $this->obTransacao                      = new Transacao;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        list( $diaI,$mesI,$anoI ) = explode( '/', $this->getDtValidadeInicial() );
        list( $diaF,$mesF,$anoF ) = explode( '/', $this->getDtValidadeFinal() );
        if ("$anoI$mesI$diaI" < $this->stExercicio."0101") {
            $obErro->setDescricao("A data de reserva deve ser maior que o dia 01/01/".$this->stExercicio."!");
        }
        if ( !$obErro->ocorreu() ) {
            if ("$anoI$mesI$diaI" > "$anoF$mesF$diaF") {
                $obErro->setDescricao("A data de reserva deve ser menor que a data da validade!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ( $this->stExercicio.date('md') > "$anoF$mesF$diaF" ) {
                $obErro->setDescricao("A data de validade final deve ser maior ou igual ao dia de hoje!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ("$anoF$mesF$diaF" > $this->stExercicio."1231") {
                $obErro->setDescricao("A data de validade final não pode ser maior que 31/12/".$this->stExercicio."!");
            }
        }
        if ( !$obErro->ocorreu() ) {
            if ( $this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade() ) {
                $this->obROrcamentoDespesa->setExercicio( $this->stExercicio);
                $this->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );
                $stNomDespesa = $rsDespesa->getCampo( "descricao" );
                if (!$stNomDespesa) {
                    $obErro->setDescricao("Entidade informada deve ser igual a da dotação!");
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obTOrcamentoReservaSaldos->setDado( "exercicio"           , $this->stExercicio         );
            $obTOrcamentoReservaSaldos->proximoCod( $this->inCodReserva , $boTransacao );
            $obTOrcamentoReservaSaldos->setDado( "cod_reserva"         , $this->inCodReserva        );
            $obTOrcamentoReservaSaldos->setDado( "cod_despesa"         , $this->obROrcamentoDespesa->getCodDespesa()        );
            $obTOrcamentoReservaSaldos->setDado( "dt_validade_inicial" , $this->stDtValidadeInicial );
            $obTOrcamentoReservaSaldos->setDado( "dt_validade_final"   , $this->stDtValidadeFinal   );
            $obTOrcamentoReservaSaldos->setDado( "dt_inclusao"         , $this->stDtInclusao        );
            $obTOrcamentoReservaSaldos->setDado( "vl_reserva"          , $this->nuVlReserva         );
            $obTOrcamentoReservaSaldos->setDado( "tipo"                , $this->stTipo              );
            $obTOrcamentoReservaSaldos->setDado( "motivo"              , $this->stMotivo            );
            $obErro = $obTOrcamentoReservaSaldos->inclusao( $boTransacao );
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReservaSaldos );

    return $obErro;
}

/**
    * Inclui dados no banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function anular($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldosAnulada.class.php" );
    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        list( $diaA,$mesA,$anoA ) = explode( '/', $this->getDtAnulacao() );
        list( $diaI,$mesI,$anoI ) = explode( '/', $this->getDtValidadeInicial() );
        list( $diaF,$mesF,$anoF ) = explode( '/', $this->getDtValidadeFinal() );
        if ($anoA.$mesA.$diaA < $anoI.$mesI.$diaI) {
            $obErro->setDescricao("A data de anulação não pode ser inferior a data da reserva!");
        }

        if ("$anoA,$mesA,$diaA" > "$anoF,$mesF,$diaF") {
            $obErro->setDescricao("A data de anulação não pode ser superior a data de validade!");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReservaSaldosAnulada->setDado( "exercicio"           , $this->stExercicio         );
        $obTOrcamentoReservaSaldosAnulada->setDado( "cod_reserva"         , $this->inCodReserva        );
        $obTOrcamentoReservaSaldosAnulada->setDado( "dt_anulacao"         , $this->stDtAnulacao        );
        $obTOrcamentoReservaSaldosAnulada->setDado( "motivo_anulacao"     , $this->stMotivoAnulacao    );
        $obErro = $obTOrcamentoReservaSaldosAnulada->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReservaSaldosAnulada );

    return $obErro;
}

/**
    * Altera dados do banco
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReservaSaldos->setDado( "exercicio"           , $this->stExercicio         );
        $obTOrcamentoReservaSaldos->setDado( "cod_reserva"         , $this->inCodReserva        );
        $obTOrcamentoReservaSaldos->setDado( "cod_despesa"         , $this->obROrcamentoDespesa->getCodDespesa()        );
        $obTOrcamentoReservaSaldos->setDado( "dt_validade_inicial" , $this->stDtValidadeInicial );
        $obTOrcamentoReservaSaldos->setDado( "dt_validade_final"   , $this->stDtValidadeFinal   );
        $obTOrcamentoReservaSaldos->setDado( "dt_inclusao"         , $this->stDtInclusao        );
        $obTOrcamentoReservaSaldos->setDado( "vl_reserva"          , $this->nuVlReserva         );
        $obTOrcamentoReservaSaldos->setDado( "tipo"                , $this->stTipo              );
        $obTOrcamentoReservaSaldos->setDado( "motivo"              , $this->stMotivo            );
        $obErro = $obTOrcamentoReservaSaldos->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReservaSaldos );

    return $obErro;
}

/**
    * Exclui dados de Reserva
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReservaSaldos->setDado("cod_reserva" , $this->inCodReserva );
        $obTOrcamentoReservaSaldos->setDado("exercicio"   , $this->stExercicio  );
        $obErro = $obTOrcamentoReservaSaldos->exclusao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReservaSaldos );
    }

    return $obErro;
}

/**
    * Exclui dados de Reserva
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirAnulacao($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldosAnulada.class.php" );
    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReservaSaldosAnulada->setDado("cod_reserva" , $this->inCodReserva );
        $obTOrcamentoReservaSaldosAnulada->setDado("exercicio"   , $this->stExercicio  );
        $obErro = $obTOrcamentoReservaSaldosAnulada->exclusao( $boTransacao );
//'$obErro = $obTOrcamentoReservaSaldosAnulada->debug();
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReservaSaldosAnulada );
    }

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
function listarReservaSaldos(&$rsLista, $stOrder = "cod_reserva", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;

    $stFiltro = "";
    if( $this->inCodReserva )
        $stFiltro .= " cod_reserva = ".$this->inCodReserva." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoDespesa->getCodDespesa() )
        $stFiltro .= " cod_despesa = '".$this->obROrcamentoDespesa->getCodDespesa()."' AND ";
/*    if( $this->stDtValidadeInicial )
       // $stFiltro .= " TO_DATE(TO_CHAR(dt_validade_inicial,'dd/mm/yyyy'),'dd/mm/yyyy') >= TO_DATE('".$this->stDtValidadeInicial."', 'dd-mm-yyyy') AND ";
        $stFiltro .= " TO_DATE(dt_validade_inicial,'dd/mm/yyyy') >= TO_DATE('".$this->stDtValidadeInicial."', 'dd-mm-yyyy') AND ";
    if( $this->stDtValidadeFinal )
       // $stFiltro .= " TO_DATE(TO_CHAR(dt_validade_inicial,'dd/mm/yyyy'),'dd/mm/yyyy') <= TO_DATE('".$this->stDtValidadeFinal."', 'dd-mm-yyyy') AND ";
        $stFiltro .= " TO_DATE(dt_validade_inicial,'dd/mm/yyyy') <= TO_DATE('".$this->stDtValidadeFinal."', 'dd-mm-yyyy
') AND ";*/

    if( $this->stTipo )
        $stFiltro .= " tipo = '".$this->stTipo."' AND ";
    if( $this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " cod_entidade IN (".$this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";
    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso() )
        $stFiltro .= " cod_recurso = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()." AND ";

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaoRecurso() )
        $stFiltro .= " masc_recurso_red like '".$this->obROrcamentoDespesa->obROrcamentoRecurso->getDestinacaRecurso()."%' AND ";

    if( $this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento() )
        $stFiltro .= " cod_detalhamento = ".$this->obROrcamentoDespesa->obROrcamentoRecurso->getCodDetalhamento()." AND ";

    if ($this->stSituacao=='inativas') {
        $stFiltro .= " situacao='Inativa' AND ";
        $stFiltro .= " NOT EXISTS ( SELECT 1                                      \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio = tabela.exercicio ) AND ";
    }
    if ($this->stSituacao=='anuladas') {
        $stFiltro .= " situacao='Anulada' AND ";
        $stFiltro .= "  EXISTS ( SELECT 1                                         \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio = tabela.exercicio ) AND ";

    }
    if ($this->stSituacao=='ativas') {
        $stFiltro .= " situacao='Ativa' AND \n";
        $stFiltro .= " NOT EXISTS ( SELECT 1                                      \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio = tabela.exercicio ) AND ";
    }

    if ($this->stSituacao != 'inativas') {
        if ($this->stDtValidadeInicial && $this->stDtValidadeFinal) {
              $stFiltro .= "\n";
              $stFiltro .= "( ( TO_DATE(dt_validade_inicial, 'dd/mm/yyyy') >= TO_DATE('".$this->stDtValidadeInicial."', 'dd/mm/yyyy') AND \n";
              $stFiltro .= "    TO_DATE(dt_validade_inicial, 'dd/mm/yyyy') <= TO_DATE('".$this->stDtValidadeFinal."',   'dd/mm/yyyy') ) ";
              $stFiltro .= "\n OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_final  , 'dd/mm/yyyy') >= TO_DATE('".$this->stDtValidadeInicial."', 'dd/mm/yyyy') AND \n";
              $stFiltro .= "    TO_DATE(dt_validade_final  , 'dd/mm/yyyy') <= TO_DATE('".$this->stDtValidadeFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= "\n OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial, 'dd/mm/yyyy') >= TO_DATE('".$this->stDtValidadeInicial."', 'dd/mm/yyyy') AND \n";
              $stFiltro .= "    TO_DATE(dt_validade_final  , 'dd/mm/yyyy') <= TO_DATE('".$this->stDtValidadeFinal."',   'dd/mm/yyyy') )  ";
              $stFiltro .= "\n OR \n";
              $stFiltro .= "  ( TO_DATE(dt_validade_inicial, 'dd/mm/yyyy') <= TO_DATE('".$this->stDtValidadeInicial."', 'dd/mm/yyyy') AND \n";
              $stFiltro .= "    TO_DATE(dt_validade_final  , 'dd/mm/yyyy') >= TO_DATE('".$this->stDtValidadeFinal."',   'dd/mm/yyyy') ) ) AND ";
        }
    }

    if ($this->boAnular) {
        $stFiltro .= " NOT EXISTS ( SELECT 1 \n";
        $stFiltro .= "                FROM orcamento.reserva_saldos_anulada o_rsa \n";
        $stFiltro .= "               WHERE o_rsa.cod_reserva = tabela.cod_reserva \n";
        $stFiltro .= "                 AND o_rsa.exercicio   = tabela.exercicio   \n";
        $stFiltro .= "            ) \n";
        $stFiltro .= " AND \n";
        $stFiltro .= " dt_anulacao is null AND to_date(dt_validade_final, 'dd/mm/yyyy') > to_date('".date('d-m')."-".$this->stExercicio."', 'dd/mm/yyyy') AND ";
    }
    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $obTOrcamentoReservaSaldos->setDado( 'exercicio' , $this->getExercicio() );
    $obTOrcamentoReservaSaldos->setDado( 'stFiltro'  , $stFiltro );
    $obTOrcamentoReservaSaldos->setDado( 'dataAtual'  , $this->stExercicio.date('-m-d') ); // ????
    if (!$this->stDtValidadeInicial && !$this->stDtValidadeFinal) {
        if (date('Y') > Sessao::getExercicio() && Sessao::read('data_reserva_saldo_GF')) {
            $arData = explode('-', Sessao::read('data_reserva_saldo_GF'));
            $stDataAtual = $arData[2].'/'.$arData[1].'/'.$arData[0];
            $obTOrcamentoReservaSaldos->setDado( 'stDtInicial', $stDataAtual);
            $obTOrcamentoReservaSaldos->setDado( 'stDtFinal',   $stDataAtual);
        } else {
            $obTOrcamentoReservaSaldos->setDado( 'stDtInicial', date('d/m/').$this->stExercicio);
            $obTOrcamentoReservaSaldos->setDado( 'stDtFinal',   date('d/m/').$this->stExercicio);
        }
    } else {
        $obTOrcamentoReservaSaldos->setDado( 'stDtInicial', $this->stDtValidadeInicial);
        $obTOrcamentoReservaSaldos->setDado( 'stDtFinal',   $this->stDtValidadeFinal);
    }


    $stOrder = ($stOrder) ? $stOrder : 'cod_reserva';
    $obErro = $obTOrcamentoReservaSaldos->recuperaRelacionamento( $rsLista, '', $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Lista todas as Reservas de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "cod_reserva", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;

    $stFiltro = "";

    if( $this->inCodReserva )
        $stFiltro .= " cod_reserva = ".$this->inCodReserva." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    if( $this->stDtValidadeInicial )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_validade_inicial,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtValidadeInicial."', 'dd-mm-yyyy') AND ";
    if( $this->stDtValidadeFinal )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_validade_final,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtValidadeFinal."', 'dd-mm-yyyy') AND ";
    if( $this->stDtInclusao )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_inclusao,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtInclusao."', 'dd-mm-yyyy') AND ";
    if( $this->nuVlReserva )
        $stFiltro .= " vl_reserva = ".$this->nuVlReserva." AND ";
    if( $this->stTipo )
        $stFiltro .= " tipo = '".$this->stTipo."' AND ";
    if( $this->stMotivo )
        $stFiltro .= " motivo = '".$this->stMotivo."' AND ";

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? $stOrder : 'cod_reserva';
    $obErro = $obTOrcamentoReservaSaldos->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Lista todas as Reservas Anuladas de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAnuladas(&$rsLista, $stOrder = "cod_reserva", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldosAnulada.class.php" );
    $obTOrcamentoReservaSaldosAnulada = new TOrcamentoReservaSaldosAnulada;

    $stFiltro = "";

    if( $this->inCodReserva )
        $stFiltro .= " cod_reserva = ".$this->inCodReserva." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
/*    if( $this->stDtAnulacao )
        $stFiltro .= " TO_DATE(TO_CHAR(dt_anulacao,'dd/mm/yyyy'),'dd/mm/yyyy'') = TO_DATE('".$this->stDtAnulacao."', 'dd-mm-yyyy') AND ";
    if( $this->stMotivoAnulacao )
        $stFiltro .= " motivo_anulacao = '".$this->stMotivoAnulacao."' AND ";*/

    $stFiltro = ($stFiltro) ? ' WHERE '.substr($stFiltro,0,(strlen($stFiltro)-4)):'';
    $stOrder = ($stOrder) ? $stOrder : 'cod_reserva';
    $obErro = $obTOrcamentoReservaSaldosAnulada->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReservaSaldos.class.php"        );
    $obTOrcamentoReservaSaldos        = new TOrcamentoReservaSaldos;

    $obTOrcamentoReservaSaldos->setDado( "cod_reserva" , $this->inCodReserva );
    $obTOrcamentoReservaSaldos->setDado( "exercicio"   , $this->stExercicio  );
    $obErro = $obTOrcamentoReservaSaldos->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stDtValidadeInicial = $rsRecordSet->getCampo("dt_validade_inicial");
        $this->stDtValidadeFinal   = $rsRecordSet->getCampo("dt_validade_final"  );
        $this->stDtInclusao        = $rsRecordSet->getCampo("dt_inclusao"        );
        $this->nuVlReserva         = $rsRecordSet->getCampo("vl_reserva"         );
        $this->stTipo              = $rsRecordSet->getCampo("tipo"               );
        $this->stMotivo            = $rsRecordSet->getCampo("motivo"             );
        $this->obROrcamentoDespesa->setCodDespesa($rsRecordSet->getCampo("cod_despesa"));

    }

    return $obErro;
}

}
