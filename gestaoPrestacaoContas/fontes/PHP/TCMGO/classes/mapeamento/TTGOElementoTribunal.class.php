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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 09/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.2  2007/10/11 00:41:56  hboaventura
correção dos arquivos

Revision 1.1  2007/10/10 23:35:33  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOElementoTribunal extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */

    public function TTGOElementoTribunal()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.elemento_tribunal");

        $this->setCampoCod('estrutural');
        $this->setComplementoChave('');

        $this->AddCampo( 'estrutural' ,'varchar' ,true, '150'   ,true ,false  );
        $this->AddCampo( 'descricao','varchar' ,true, '160' ,false,false );
    }

    public function recuperaElementoDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaElementoDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaElementoDespesa()
    {
        $stSql = "
            SELECT  conta_despesa.cod_estrutural
                 ,  conta_despesa.descricao
                 ,  conta_despesa.cod_conta
                 ,  REPLACE(conta_despesa.cod_estrutural,'.','') AS estrutural
              FROM  orcamento.conta_despesa
        INNER JOIN  empenho.pre_empenho_despesa
                ON  pre_empenho_despesa.exercicio = conta_despesa.exercicio
               AND  pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
             WHERE  conta_despesa.exercicio = '".$this->getDado('exercicio')."'
               AND  EXISTS ( SELECT 1
                               FROM tcmgo.elemento_tribunal
                              WHERE SUBSTR(REPLACE(elemento_tribunal.estrutural,'.',''),1,6) = SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)
                           )
          GROUP BY  conta_despesa.cod_estrutural
                 ,  conta_despesa.descricao
                 ,  conta_despesa.cod_conta
          ORDER BY  cod_estrutural
        ";

        return $stSql;
    }

    public function recuperaElementoDespesaTribunal(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaElementoDespesaTribunal",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaElementoDespesaTribunal()
    {
        $stSql = "
            SELECT  elemento_tribunal.estrutural
                 ,  elemento_tribunal.descricao
                 ,  elemento_de_para.cod_conta
              FROM  tcmgo.elemento_tribunal
         LEFT JOIN  tcmgo.elemento_de_para
                ON  elemento_de_para.cod_conta = ".$this->getDado('cod_conta')."
               AND  elemento_de_para.exercicio = '".$this->getDado('exercicio')."'
               AND  elemento_de_para.estrutural = elemento_tribunal.estrutural
             WHERE  substr(replace(elemento_tribunal.estrutural,'.',''),1,6) = substr('".$this->getDado('cod_estrutural')."',1,6)
/*          GROUP BY  elemento_tribunal.estrutural
                 ,  elemento_tribunal.descricao
                 ,  elemento_de_para.cod_conta*/
          ORDER BY  estrutural
        ";

        return $stSql;
    }

}
