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
    * Classe de regra de negocio para MONETARIO.ACRESCIMO_NORMA
    * Data de Criacao: 27/11/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Regra

    * $Id: $

* Casos de uso: uc-05.05.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONAcrescimoNorma extends Persistente
{
    /**
        * Metodo Construtor
        * @access Private
    */
    public function TMONAcrescimoNorma()
    {
        parent::Persistente();
        $this->setTabela('monetario.acrescimo_norma');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_acrescimo,cod_tipo,timestamp');

        $this->AddCampo('cod_acrescimo','integer',true,'',true,true);
        $this->AddCampo('cod_tipo','integer',true,'',true,true);
        $this->AddCampo('timestamp','timestamp',false,'',true,false);
        $this->AddCampo('cod_norma','integer',true,'',false,true);
    }

    public function listaAcrescimoNorma(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaAcrescimoNorma().$stFiltro;
        $this->stDebug = $stSql;
        #$this->debug(); exit;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaAcrescimoNorma()
    {
        $stSql = "
            SELECT
                norma.nom_norma,
                acrescimo_norma.cod_norma

            FROM
                monetario.acrescimo_norma

            INNER JOIN
                normas.norma
            ON
                norma.cod_norma = acrescimo_norma.cod_norma ";

        return $stSql;
    }

}// fecha classe de mapeamento
