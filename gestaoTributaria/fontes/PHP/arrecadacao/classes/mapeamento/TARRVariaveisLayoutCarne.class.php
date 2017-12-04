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
    * Classe de mapeamento da tabela ARRECADACAO.VARIAVEIS_LAYOUT_CARNE
    * Data de Criação: 03/10/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: $

* Casos de uso: uc-05.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRVariaveisLayoutCarne extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TARRVariaveisLayoutCarne()
    {
        parent::Persistente();
        $this->setTabela( 'arrecadacao.variaveis_layout_carne' );

        $this->setCampoCod( '' );
        $this->setComplementoChave( 'cod_modelo, cod_modulo, cod_cadastro, cod_atributo' );

        $this->AddCampo( 'cod_modelo', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_modulo', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_cadastro', 'integer', true, '', true, true );
        $this->AddCampo( 'cod_atributo', 'integer', true, '', true, true );
        $this->AddCampo( 'ordem', 'integer', true, '', false, false );
        $this->AddCampo( 'posicao_inicial', 'integer', true, '', false, false );
        $this->AddCampo( 'largura', 'integer', true, '', false, false );
    }

    public function recuperaVariaveisLayout(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaVariaveisLayout().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaVariaveisLayout()
    {
        $stSql = "
            SELECT
                atributo_dinamico.nom_atributo,
                variaveis_layout_carne.*

            FROM
                arrecadacao.variaveis_layout_carne

            INNER JOIN
                administracao.atributo_dinamico
            ON
                atributo_dinamico.cod_modulo = variaveis_layout_carne.cod_modulo
                AND atributo_dinamico.cod_cadastro = variaveis_layout_carne.cod_cadastro
                AND atributo_dinamico.cod_atributo = variaveis_layout_carne.cod_atributo
        ";

        return $stSql;
    }
}
?>
