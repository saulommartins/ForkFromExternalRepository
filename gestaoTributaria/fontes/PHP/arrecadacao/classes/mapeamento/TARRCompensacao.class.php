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
    * Classe de mapeamento da tabela ARRECADACAO.COMPENSACAO
    * Data de Criação: 10/12/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCompensacao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRCompensacao extends Persistente
{
    public function TARRCompensacao()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.compensacao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_compensacao');

        $this->AddCampo( 'cod_compensacao', 'integer', true, '', true, true);
        $this->AddCampo( 'numcgm', 'integer', true, '', false, true);
        $this->AddCampo( 'timestamp', 'timestamp', false, '', false, false);
        $this->AddCampo( 'valor', 'numeric', true, '14,2', false, false);
        $this->AddCampo( 'aplicar_acrescimos', 'boolean', true, '', false, false);
        $this->AddCampo( 'cod_tipo', 'integer', true, '', false, true);
    }

    public function recuperaProximoCodigoCompensacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaProximoCodigoCompensacao();
        $this->stDebug = $stSql;
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProximoCodigoCompensacao()
    {
        $stSql = "  SELECT
                        max(cod_compensacao) AS max_cod
                    FROM
                        arrecadacao.compensacao ";

        return $stSql;
    }

}// end of class
?>
