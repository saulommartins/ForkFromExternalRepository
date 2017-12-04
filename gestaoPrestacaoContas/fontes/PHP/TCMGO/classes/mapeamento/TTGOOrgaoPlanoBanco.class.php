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
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOOrgaoPlanoBanco extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TTGOOrgaoPlanoBanco()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.orgao_plano_banco");

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('num_orgao,cod_plano');

        $this->AddCampo( 'num_orgao' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio','varchar' ,true, '4' ,true,true );
        $this->AddCampo( 'cod_plano','integer' ,true, '' ,true,true );
    }

    public function recuperaPlanoBanco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPlanoBanco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPlanoBanco()
    {
        $stSql = "
            SELECT  cod_plano
                 ,  nom_banco
                 ,  num_banco
                 ,  nom_agencia
                 ,  num_agencia
                 ,  conta_corrente
              FROM  contabilidade.plano_banco
        INNER JOIN  monetario.banco
                ON  banco.cod_banco = plano_banco.cod_banco
        INNER JOIN  monetario.agencia
                ON  agencia.cod_agencia = plano_banco.cod_agencia
        ";

        return $stSql;
    }

    public function recuperaBanco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaBanco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaBanco()
    {
        $stSql = "
            SELECT  nom_banco
                 ,  num_banco
                 ,  cod_banco
              FROM  monetario.banco
             WHERE  banco.nom_banco <> ' '
          ORDER BY  num_banco
        ";

        return $stSql;
    }

    public function recuperaAgencia(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaAgencia",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaAgencia()
    {
        $stSql = "
            SELECT  cod_banco
                 ,  cod_agencia
                 ,  nom_agencia
                 ,  num_agencia
              FROM  monetario.agencia
             WHERE  cod_banco = ".$this->getDado('cod_banco')."
               AND  nom_agencia <> ' '
          ORDER BY  nom_agencia
        ";

        return $stSql;
    }

    public function recuperaContaCorrente(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContaCorrente",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContaCorrente()
    {
        $stSql = "
            SELECT plano_banco.cod_plano
                 , conta_corrente
                 , tipo_conta.descricao as descricao_tipo_conta
              FROM contabilidade.plano_banco
        INNER JOIN monetario.conta_corrente
                ON plano_banco.cod_banco = conta_corrente.cod_banco
               AND plano_banco.cod_agencia = conta_corrente.cod_agencia
               AND plano_banco.cod_conta_corrente = conta_corrente.cod_conta_corrente
        INNER JOIN monetario.tipo_conta
                ON tipo_conta.cod_tipo = conta_corrente.cod_tipo
             WHERE plano_banco.cod_banco = ".$this->getDado('cod_banco')."
               AND plano_banco.cod_agencia = ".$this->getDado('cod_agencia')."
               AND plano_banco.exercicio = '".$this->getDado('exercicio')."'
          ORDER BY conta_corrente
                 , plano_banco.cod_plano";

        return $stSql;
    }

    public function recuperaOrgaoPlanoBanco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaOrgaoPlanoBanco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgaoPlanoBanco()
    {
        $stSql = "
            SELECT  plano_banco.cod_plano
                 ,  nom_banco
                 ,  num_banco
                 ,  banco.cod_banco
                 ,  nom_agencia
                 ,  num_agencia
                 ,  agencia.cod_agencia
                 ,  conta_corrente
                 ,  num_orgao
              FROM  contabilidade.plano_banco
        INNER JOIN  monetario.banco
                ON  banco.cod_banco = plano_banco.cod_banco
        INNER JOIN  monetario.agencia
                ON  agencia.cod_agencia = plano_banco.cod_agencia
               AND  agencia.cod_banco = plano_banco.cod_banco
        INNER JOIN  tcmgo.orgao_plano_banco
                ON  orgao_plano_banco.exercicio = plano_banco.exercicio
               AND  orgao_plano_banco.cod_plano = plano_banco.cod_plano
               AND  orgao_plano_banco.num_orgao = ".$this->getDado('num_orgao')."
             WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
          ORDER BY  num_banco
                 ,  conta_corrente
                 ,  plano_banco.cod_plano

        ";

        return $stSql;
    }
}
