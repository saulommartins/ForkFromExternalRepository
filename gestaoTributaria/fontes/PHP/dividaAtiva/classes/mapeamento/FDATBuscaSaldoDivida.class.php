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
/*
* Classe de mapeamento para a função fn_busca_saldo_divida
*
* Data de Criação   : 07/06/2010
*
* @author Analista      Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor Cassiano de Vasconcellos Ferreira
*
* @package URBEM
* @subpackage
*
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class FDATBuscaSaldoDivida extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FDATBuscaSaldoDivida()
    {
        parent::Persistente();
        $this->setTabela('divida.fn_busca_saldo_divida');
    }
#fn_busca_saldo_divida(
//inNumCGM INTEGER,
//inInscMunIni INTEGER,
//inInscMunFim INTEGER,
//inInscEcoIni INTEGER,
//inInscEcoFim INTEGER,
//inInscDivida INTEGER,
//stExercicio VARCHAR)

    public function montaRecuperaTodos()
    {
        $stFiltro  = $this->getDado('inNumCGM') ? $this->getDado('inNumCGM').',' : 'null,';
        $stFiltro .= $this->getDado('inInscMunIni') ? $this->getDado('inInscMunIni').',' : 'null,';
        $stFiltro .= $this->getDado('inInscMunFim') ? $this->getDado('inInscMunFim').',' : 'null,';
        $stFiltro .= $this->getDado('inInscEcoIni') ? $this->getDado('inInscEcoIni').',' : 'null,';
        $stFiltro .= $this->getDado('inInscEcoFim') ? $this->getDado('inInscEcoFim').',' : 'null,';
        $stFiltro .= $this->getDado('inInscDivida') ? $this->getDado('inInscDivida').',': 'null,';
        $stFiltro .= $this->getDado('stExercicio') ? "'".$this->getDado('stExercicio')."'," : "'',";
        $stFiltro .= $this->getDado('boAgrupa') != '' ? $this->getDado('boAgrupa') : "false";

        $stSql  = 'SELECT cod_inscricao
                        , exercicio
                        , dt_vencimento_origem
                        , total_parcelas_divida
                        , inscricao
                        , inscricao_tipo
                        , cod_especie
                        , cod_genero
                        , cod_natureza
                        , cod_credito
                        , credito_formatado
                        , origem
                        , descricao_credito
                        , valor
                        , valor_corrigido
                        , numcgm
                        , nom_cgm
                     FROM '.$this->getTabela()."($stFiltro)
                       AS (
                          cod_inscricao          INTEGER
                        , exercicio              CHARACTER(4)
                        , dt_vencimento_origem   DATE
                        , total_parcelas_divida  INTEGER
                        , inscricao              INTEGER
                        , inscricao_tipo         TEXT
                        , cod_especie            INTEGER
                        , cod_genero             INTEGER
                        , cod_natureza           INTEGER
                        , cod_credito            INTEGER
                        , credito_formatado      TEXT
                        , origem                 VARCHAR
                        , descricao_credito      TEXT
                        , valor                  NUMERIC
                        , valor_corrigido        NUMERIC
                        , numcgm                 INTEGER
                        , nom_cgm                VARCHAR(200) ) ";

        return $stSql;
    }

}// end of class

?>
