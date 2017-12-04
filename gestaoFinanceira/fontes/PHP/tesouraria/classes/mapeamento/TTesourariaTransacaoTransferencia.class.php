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
    * Classe de mapeamento da tabela TESOURARIA_TRANSACOES_TRANSFERENCIA
    * Data de Criação: 24/01/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.5  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_TRANSACOES_TRANSFERENCIA
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Jose Eduardo Porto

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaTransacaoTransferencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaTransacaoTransferencia()
{
    parent::Persistente();

    $this->setTabela("tesouraria.transacoes_transferencia");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_bordero,cod_entidade,numcgm,exercicio,cod_tipo,cod_agencia,cod_banco,conta_corrente');

    $this->AddCampo('cod_bordero'        , 'integer'  , true, ''    , true  , true  );
    $this->AddCampo('cod_entidade'       , 'integer'  , true, ''    , true  , true  );
    $this->AddCampo('numcgm'             , 'integer'  , true, ''    , true  , true  );
    $this->AddCampo('exercicio'          , 'varchar'  , true, '04'  , true  , true  );
    $this->AddCampo('cod_tipo'           , 'integer'  , true, ''    , true  , true  );
    $this->AddCampo('cod_agencia'        , 'integer'  , true, ''    , true  , true  );
    $this->AddCampo('cod_banco'          , 'integer'  , true, ''    , true  , true  );
    $this->AddCampo('conta_corrente'     , 'varchar'  , true, '20'  , true  , false );
    $this->AddCampo('documento'          , 'varchar'  , true, '100' , true  , false );
    $this->AddCampo('descricao'          , 'text'     , true, ''    , false , false );
    $this->AddCampo('valor'              , 'float'    , true, '14,2', false , false );
    $this->AddCampo('cod_plano'          , 'integer'  , true, ''    , true  , true  );
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                                                           \n";
    $stSql .= "        BOLETIM.cod_boletim,                                                     \n";
    $stSql .= "        BOLETIM.cod_entidade,                                                    \n";
    $stSql .= "        BOLETIM.exercicio,                                                       \n";
    $stSql .= "        BOLETIM.cod_terminal,                                                    \n";
    $stSql .= "        BOLETIM.timestamp_terminal,                                              \n";
    $stSql .= "        BOLETIM.cgm_usuario,                                                     \n";
    $stSql .= "        BOLETIM.timestamp_usuario,                                               \n";
    $stSql .= "        TO_CHAR(BOLETIM.dt_boletim, 'dd/mm/yyyy') AS dt_boletim,                 \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        TB.cod_bordero,                                                          \n";
    $stSql .= "        TB.cod_entidade,                                                         \n";
    $stSql .= "        TB.exercicio AS exercicio_bordero,                                       \n";
    $stSql .= "        TB.cod_boletim,                                                          \n";
    $stSql .= "        TB.exercicio_boletim,                                                    \n";
    $stSql .= "        TO_CHAR(TB.timestamp_bordero,'dd/mm/yyyy') AS dt_bordero,                \n";
    $stSql .= "        TB.cod_plano,                                                            \n";
    $stSql .= "        TB.cod_terminal,                                                         \n";
    $stSql .= "        TB.cgm_usuario,                                                          \n";
    $stSql .= "        TB.timestamp_terminal,                                                   \n";
    $stSql .= "        TB.timestamp_usuario,                                                    \n";
    $stSql .= "        CGM.nom_cgm AS nom_cgm_bordero,                                          \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        TTT.cod_bordero,                                                         \n";
    $stSql .= "        TTT.cod_entidade,                                                        \n";
    $stSql .= "        TTT.numcgm AS numcgm_transferencia,                                      \n";
    $stSql .= "        TTT.exercicio,                                                           \n";
    $stSql .= "        TTT.cod_plano,                                                           \n";
    $stSql .= "        TTT.descricao,                                                           \n";
    $stSql .= "        TTT.valor,                                                               \n";
    $stSql .= "        TTT.documento,                                                           \n";
    $stSql .= "        TTT.cod_tipo,                                                            \n";
    $stSql .= "        TTT.cod_agencia,                                                         \n";
    $stSql .= "        TTT.cod_banco,                                                           \n";
    $stSql .= "        TTT.conta_corrente AS conta_corrente_transferencia,                      \n";
    $stSql .= "        MAT.num_agencia AS num_agencia_transferencia,                            \n";
    $stSql .= "        MBT.num_banco AS num_banco_transferencia,                                \n";
    $stSql .= "        CGMT.nom_cgm AS nom_cgm_transferencia,                                   \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        CASE WHEN PJ.numcgm IS NULL THEN                                         \n";
    $stSql .= "            PF.cpf                                                               \n";
    $stSql .= "        ELSE                                                                     \n";
    $stSql .= "            PJ.cnpj                                                              \n";
    $stSql .= "        END AS inscricao                                                         \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "FROM    tesouraria.boletim AS BOLETIM                                            \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "        INNER JOIN tesouraria.bordero AS TB  ON (                                \n";
    $stSql .= "                    TB.cod_boletim            = BOLETIM.cod_boletim              \n";
    $stSql .= "            AND     TB.cod_entidade           = BOLETIM.cod_entidade             \n";
    $stSql .= "            AND     TB.exercicio_boletim      = BOLETIM.exercicio                \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        INNER JOIN orcamento.entidade AS OE ON (                                 \n";
    $stSql .= "                    OE.cod_entidade           = TB.cod_entidade                  \n";
    $stSql .= "            AND     OE.exercicio              = TB.exercicio                     \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        INNER JOIN sw_cgm AS CGM ON (                                            \n";
    $stSql .= "                    CGM.numcgm                = OE.numcgm                        \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        INNER JOIN tesouraria.transacoes_transferencia AS TTT ON (               \n";
    $stSql .= "                    TTT.cod_bordero    = TB.cod_bordero                          \n";
    $stSql .= "            AND     TTT.cod_entidade   = TB.cod_entidade                         \n";
    $stSql .= "            AND     TTT.exercicio      = TB.exercicio                            \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        INNER JOIN monetario.agencia AS MAT ON (                                 \n";
    $stSql .= "                    MAT.cod_banco              = TTT.cod_banco                   \n";
    $stSql .= "            AND     MAT.cod_agencia            = TTT.cod_agencia                 \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        INNER JOIN monetario.banco AS MBT ON (                                   \n";
    $stSql .= "                    MBT.cod_banco              = MAT.cod_banco                   \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        INNER JOIN sw_cgm AS CGMT ON (                                           \n";
    $stSql .= "                    CGMT.numcgm                 = TTT.numcgm                     \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        LEFT OUTER JOIN sw_cgm_pessoa_fisica AS PF ON (                          \n";
    $stSql .= "                    PF.numcgm                 = CGMT.numcgm                      \n";
    $stSql .= "        )                                                                        \n";
    $stSql .= "        LEFT OUTER JOIN sw_cgm_pessoa_juridica AS PJ ON (                        \n";
    $stSql .= "                    PJ.numcgm                 = CGMT.numcgm                      \n";
    $stSql .= "        )                                                                        \n";

    return $stSql;
}

}
