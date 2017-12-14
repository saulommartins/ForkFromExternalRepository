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
    * Classe de mapeamento da tabela DIVIDA.AUTORIDADE
    * Data de Criação: 14/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATAutoridade.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.08
*/

/*
$Log$
Revision 1.3  2006/09/26 11:11:12  dibueno
adição de mais um campo no SQL de busca, concatenado o nom_cgm e numcgm no alias autoridade

Revision 1.2  2006/09/22 09:59:49  cercato
correcao do caso de uso.

Revision 1.1  2006/09/18 17:19:17  cercato
classes de mapeamento para as tabelas "autoridade" e "procurador".

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATAutoridade extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATAutoridade()
    {
        parent::Persistente();
        $this->setTabela('divida.autoridade');

        $this->setCampoCod('cod_autoridade');
        $this->setComplementoChave('');

        $this->AddCampo('cod_autoridade','integer',true,'',true,false);
        $this->AddCampo('cod_contrato','integer',true,'',false,true);
        $this->AddCampo('cod_norma','integer',true,'',false,true);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('assinatura','oid',false,'',false,false);
        $this->AddCampo('tipo_assinatura','varchar',false,'20',false,false);
        $this->AddCampo('tamanho_assinatura','integer',false,'',false,false);
    }

    public function recuperaListaAutoridade(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaAutoridade().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaAutoridade()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     ps.numcgm, \n";
        $stSql .= "     sc.nom_cgm, \n";
        $stSql .= "     (ps.numcgm ||' - '|| sc.nom_cgm) as autoridade, \n";
        $stSql .= "     da.cod_autoridade, \n";
        $stSql .= "     da.cod_norma, \n";
        $stSql .= "     ( \n";
        $stSql .= "         SELECT \n";
        $stSql .= "             no.nom_norma \n";
        $stSql .= "         FROM \n";
        $stSql .= "             normas.norma AS no \n";
        $stSql .= "         WHERE \n";
        $stSql .= "             no.cod_norma = da.cod_norma \n";
        $stSql .= "     )AS nom_norma, \n";
        $stSql .= "     dp.oab, \n";
        $stSql .= "     ( \n";
        $stSql .= "         SELECT \n";
        $stSql .= "             su.nom_uf \n";
        $stSql .= "         FROM \n";
        $stSql .= "             sw_uf AS su \n";
        $stSql .= "         WHERE \n";
        $stSql .= "             su.cod_uf = dp.cod_uf \n";
        $stSql .= "     ) AS nom_uf, \n";
        $stSql .= "     dp.cod_uf, \n";
        $stSql .= "     CASE WHEN dp.cod_autoridade IS NOT NULL THEN \n";
        $stSql .= "         'Procurador Municipal' \n";
        $stSql .= "     ELSE \n";
        $stSql .= "         'Autoridade Competente' \n";
        $stSql .= "     END AS tipo, \n";
        $stSql .= "     pc.registro, \n";
        $stSql .= "     pca.descricao, \n";
        $stSql .= "     ( \n";
        $stSql .= "         SELECT \n";
        $stSql .= "             to_char(pc.vigencia, 'dd/mm/YYYY') \n";
        $stSql .= "         FROM \n";
        $stSql .= "             pessoal.contrato_servidor_funcao AS pc, \n";
        $stSql .= "             ( \n";
        $stSql .= "                 SELECT \n";
        $stSql .= "                     MAX(pf.timestamp) AS timestamp \n";
        $stSql .= "                 FROM \n";
        $stSql .= "                     pessoal.contrato_servidor_funcao AS pf \n";
        $stSql .= "                 WHERE \n";
        $stSql .= "                     pf.cod_contrato = da.cod_contrato \n";
        $stSql .= "                     AND pf.cod_cargo = pcs.cod_cargo \n";
        $stSql .= "             )AS temp \n";
        $stSql .= "         WHERE \n";
        $stSql .= "             pc.cod_contrato = da.cod_contrato \n";
        $stSql .= "             AND pc.cod_cargo = pcs.cod_cargo \n";
        $stSql .= "             AND pc.timestamp = temp.timestamp \n";
        $stSql .= "     )AS vigencia \n";
        $stSql .= " FROM \n";
        $stSql .= "     pessoal.servidor AS ps \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     sw_cgm AS sc \n";
        $stSql .= " ON \n";
        $stSql .= "     sc.numcgm = ps.numcgm \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     divida.autoridade AS da \n";
        $stSql .= " ON \n";
        $stSql .= "     da.numcgm = ps.numcgm \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     pessoal.contrato AS pc \n";
        $stSql .= " ON \n";
        $stSql .= "     pc.cod_contrato = da.cod_contrato \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     pessoal.contrato_servidor AS pcs \n";
        $stSql .= " ON \n";
        $stSql .= "     pcs.cod_contrato = da.cod_contrato \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     pessoal.cargo AS pca \n";
        $stSql .= " ON \n";
        $stSql .= "     pca.cod_cargo = pcs.cod_cargo \n";
        $stSql .= " LEFT JOIN \n";
        $stSql .= "     divida.procurador AS dp \n";
        $stSql .= " ON \n";
        $stSql .= "     dp.cod_autoridade = da.cod_autoridade \n";

        return $stSql;
    }

    public function recuperaListaMatricula(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaMatricula().$stCondicao.$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaMatricula()
    {
        $stSql   = " SELECT \n";
        $stSql  .= "    ps.numcgm, \n";
        $stSql  .= "    pc.registro, \n";
        $stSql  .= "    pca.descricao, \n";
        $stSql  .= "    to_char(psf.vigencia, 'dd/mm/YYYY') AS vigencia \n";
        $stSql  .= " FROM \n";
        $stSql  .= "    pessoal.servidor AS ps \n";
        $stSql  .= " INNER JOIN \n";
        $stSql  .= "    pessoal.servidor_contrato_servidor AS psc \n";
        $stSql  .= " ON \n";
        $stSql  .= "    psc.cod_servidor = ps.cod_servidor \n";
        $stSql  .= " INNER JOIN \n";
        $stSql  .= "    pessoal.contrato AS pc \n";
        $stSql  .= " ON \n";
        $stSql  .= "    pc.cod_contrato = psc.cod_contrato \n";
        $stSql  .= " INNER JOIN \n";
        $stSql  .= "    pessoal.contrato_servidor AS pcs \n";
        $stSql  .= " ON \n";
        $stSql  .= "    pcs.cod_contrato = psc.cod_contrato \n";
        $stSql  .= " INNER JOIN \n";
        $stSql  .= "    pessoal.cargo AS pca \n";
        $stSql  .= " ON \n";
        $stSql  .= "    pca.cod_cargo = pcs.cod_cargo \n";
        $stSql  .= " INNER JOIN ( \n";
        $stSql  .= "    SELECT \n";
        $stSql  .= "        tmp.* \n";
        $stSql  .= "    FROM \n";
        $stSql  .= "        pessoal.contrato_servidor_funcao AS tmp, ( \n";
        $stSql  .= "        SELECT \n";
        $stSql  .= "            max(timestamp) as timestamp, \n";
        $stSql  .= "            cod_cargo, \n";
        $stSql  .= "            cod_contrato \n";
        $stSql  .= "        FROM \n";
        $stSql  .= "            pessoal.contrato_servidor_funcao \n";
        $stSql  .= "        GROUP BY \n";
        $stSql  .= "            cod_cargo, cod_contrato \n";
        $stSql  .= "        ) AS tmp2 \n";
        $stSql  .= "    WHERE \n";
        $stSql  .= "        tmp2.timestamp = tmp.timestamp \n";
        $stSql  .= "        AND tmp2.cod_cargo = tmp.cod_cargo \n";
        $stSql  .= "        AND tmp2.cod_contrato = tmp.cod_contrato \n";
        $stSql  .= " ) AS psf \n";
        $stSql  .= " ON \n";
        $stSql  .= "    psf.cod_contrato = psc.cod_contrato \n";
        $stSql  .= "    AND psf.cod_cargo = pcs.cod_cargo \n";

        return $stSql;
    }

}// end of class

?>
