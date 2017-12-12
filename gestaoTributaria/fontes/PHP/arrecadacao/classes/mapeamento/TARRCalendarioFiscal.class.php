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
    * Classe de mapeamento da tabela ARRECADACAO.CALENDARIO_FISCAL
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCalendarioFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.03
*/

/*
$Log$
Revision 1.10  2006/10/23 10:23:10  cercato
setando ano_exercicio na consulta para recuperar grupo vencimento.

Revision 1.9  2006/10/06 11:15:00  cercato
correcao da consulta de listar grupo.

Revision 1.8  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.CALENDARIO_FISCAL
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRCalendarioFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRCalendarioFiscal()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.calendario_fiscal');

    $this->setCampoCod('cod_grupo');
    $this->setComplementoChave('ano_exercicio');

    $this->AddCampo('cod_grupo','integer',true,'',true,true);
    $this->AddCampo('valor_minimo','numeric',true,'14,2',false,false);
    $this->AddCampo('valor_minimo_lancamento','numeric',true,'14,2',false,false);
    $this->AddCampo('valor_minimo_parcela','numeric',true,'14,2',false,false);
    $this->AddCampo('ano_exercicio', 'varchar', true, '4', true, true );
}

function montaRecuperaRelacionamento()
{
    $stSql .= "   SELECT                                      \n";
    $stSql .= "       GC.COD_GRUPO,                           \n";
    $stSql .= "       GC.ANO_EXERCICIO,                       \n";
    $stSql .= "       GC.DESCRICAO AS DESCRICAO_CREDITO,      \n";
    $stSql .= "       CF.VALOR_MINIMO,                        \n";
    $stSql .= "       CF.VALOR_MINIMO_LANCAMENTO,             \n";
    $stSql .= "       CF.VALOR_MINIMO_PARCELA                 \n";
    $stSql .= "   FROM                                        \n";
    $stSql .= "       arrecadacao.grupo_credito     AS GC         \n";
    $stSql .= "   INNER JOIN                                  \n";
    $stSql .= "       arrecadacao.calendario_fiscal AS CF         \n";
    $stSql .= "   ON                                          \n";
    $stSql .= "       GC.COD_GRUPO = CF.COD_GRUPO	      \n";
    $stSql .= "   AND GC.ANO_EXERCICIO = CF.ANO_EXERCICIO   \n";

    return $stSql;
}

function recuperaGrupoVencimentos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaGrupoVencimentos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaGrupoVencimentos()
{
    $stSql .= "   SELECT                                      \n";
    $stSql .= "       GC.COD_GRUPO,                           \n";
    $stSql .= "       GV.COD_VENCIMENTO,                      \n";
    $stSql .= "       GC.ANO_EXERCICIO,                       \n";
    $stSql .= "       GC.DESCRICAO AS DESCRICAO_CREDITO,      \n";
    $stSql .= "       CF.VALOR_MINIMO,                        \n";
    $stSql .= "       CF.VALOR_MINIMO_LANCAMENTO,             \n";
    $stSql .= "       CF.VALOR_MINIMO_PARCELA,                \n";
    $stSql .= "       GV.DESCRICAO AS DESCRICAO_VENCIMENTO,   \n";
    $stSql .= "       to_char(GV.DATA_VENCIMENTO_PARCELA_UNICA,'dd/mm/yyyy') AS DATA_VENCIMENTO_PARCELA_UNICA, \n";
    $stSql .= "       GV.LIMITE_INICIAL,   \n";
    $stSql .= "       GV.LIMITE_FINAL,   \n";
    $stSql .= "       GV.UTILIZAR_UNICA   \n";

    $stSql .= "   FROM                                        \n";
    $stSql .= "       arrecadacao.grupo_credito     AS GC,        \n";
    $stSql .= "       arrecadacao.calendario_fiscal AS CF,        \n";
    $stSql .= "       arrecadacao.grupo_vencimento  AS GV         \n";
    $stSql .= "   WHERE                                       \n";
    $stSql .= "       GC.COD_GRUPO = CF.COD_GRUPO AND         \n";
    $stSql .= "       CF.COD_GRUPO = GV.COD_GRUPO AND         \n";

    $stSql .= "       GC.ANO_EXERCICIO = CF.ANO_EXERCICIO AND         \n";
    $stSql .= "       CF.ANO_EXERCICIO = GV.ANO_EXERCICIO             \n";

    return $stSql;
}

}
?>
