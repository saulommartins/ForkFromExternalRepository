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
    * Classe de regra de negocio para MONETARIO.ACRESCIMO
    * Data de Criacao: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.11
*/

/*
$Log$
Revision 1.12  2006/11/22 17:54:52  cercato
bug #7576#

Revision 1.11  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONAcrescimo extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function TMONAcrescimo()
    {
        parent::Persistente();
        $this->setTabela('monetario.acrescimo');

        $this->setCampoCod('cod_acrescimo');
        $this->setComplementoChave('cod_tipo');

        $this->AddCampo('cod_acrescimo','integer',true,'',true,false);
        $this->AddCampo('cod_tipo','integer',true,'',true,false);
        $this->AddCampo('descricao_acrescimo','varchar',true,'80',false,false);

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = " SELECT
                    ma.cod_acrescimo,
                    ma.descricao_acrescimo,
                    ma.cod_tipo,
                    mta.nom_tipo,
                    to_char(mfa.timestamp,'dd/mm/YYYY') as inicio_vigencia,
                    FUNCAO.cod_funcao,
                    FUNCAO.nom_funcao,
                    FUNCAO.cod_modulo,
                    FUNCAO.cod_biblioteca
                    FROM
                    monetario.acrescimo as ma

                    INNER JOIN
                    monetario.tipo_acrescimo as mta
                    ON
                    ma.cod_tipo = mta.cod_tipo

                    LEFT JOIN
                        (
                            SELECT
                                BAL.*
                            FROM
                                monetario.formula_acrescimo AS BAL,
                                (
                                    SELECT
                                        MAX(TIMESTAMP) AS TIMESTAMP,
                                        cod_acrescimo
                                    FROM
                                        monetario.formula_acrescimo
                                    GROUP BY
                                        cod_acrescimo
                                ) AS BT
                            WHERE
                                BAL.cod_acrescimo = BT.cod_acrescimo AND
                                BAL.timestamp = BT.timestamp
                        )as mfa
                    ON
                        mfa.cod_acrescimo = ma.cod_acrescimo

                    LEFT JOIN
                    administracao.funcao as FUNCAO
                    ON
                    FUNCAO.cod_modulo = mfa.cod_modulo
                    AND
                    FUNCAO.cod_biblioteca = mfa.cod_biblioteca
                    AND
                    FUNCAO.cod_funcao = mfa.cod_funcao
                    ";

        return $stSql;
    }

    public function listaTopAcrescimo(&$inTopCodAcrecimo, &$inTopCodTip, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaTopAcrescimo();
        $this->stDebug = $stSql;
        #$this->debug(); exit;
        $inTopCodAcrecimo = $inTopCodTip = 1;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$rsRecordSet->Eof() ) {
            $inTopCodAcrecimo = $rsRecordSet->getCampo("cod_acrescimo");
            $inTopCodTip = $rsRecordSet->getCampo("cod_tipo");
        }

        return $obErro;
    }

    public function montaListaTopAcrescimo()
    {
        $stSql = " SELECT
                        (
                            SELECT
                                max(cod_acrescimo)
                            FROM
                                monetario.acrescimo
                        ) as cod_acrescimo,
                        (
                            SELECT
                                max(cod_tipo)
                            FROM
                                monetario.acrescimo
                        ) as cod_tipo
        ";

        return $stSql;
    }

}// fecha classe de mapeamento
