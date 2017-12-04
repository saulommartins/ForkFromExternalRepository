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
    * Classe de mapeamento da tabela ARRECADACAO.INFORMACAO_ADICIONAL_LAYOUT_CARNE
    * Data de Criação: 04/11/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: $

* Casos de uso: uc-05.03.1
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRInformacaoAdicionalLayoutCarne extends Persistente
{
    /**
        * Método Construtor
        * @access Public
    */
    public function TARRInformacaoAdicionalLayoutCarne()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.informacao_adicional_layout_carne');

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo('cod_modelo', 'integer', true, '', true, true );
        $this->AddCampo('cod_informacao', 'integer', true, '', true, true );
        $this->AddCampo('ordem', 'integer', true, '', false, false );
        $this->AddCampo('posicao_inicial', 'integer', true, '', false, false );
        $this->AddCampo('largura', 'integer', true, '', false, false );
    }

    public function recuperaInfoLayout(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaInfoLayout().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInfoLayout()
    {
        $stSql = "
            SELECT
                informacao_adicional.descricao,
                funcao.nom_funcao,
                informacao_adicional_layout_carne.*

            FROM
                arrecadacao.informacao_adicional_layout_carne

            INNER JOIN
                arrecadacao.informacao_adicional
            ON
                informacao_adicional.cod_informacao = informacao_adicional_layout_carne.cod_informacao

            INNER JOIN
                administracao.funcao
            ON
                funcao.cod_modulo = informacao_adicional.cod_modulo
                AND funcao.cod_biblioteca = informacao_adicional.cod_biblioteca
                AND funcao.cod_funcao = informacao_adicional.cod_funcao
        ";

        return $stSql;
    }
}
?>
