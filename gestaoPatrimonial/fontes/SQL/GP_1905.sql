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
/*
* Script de DDL e DML
*
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* VersÃ£o 006.
*/
--------------
-- Ticket 12433
--------------

--INSERT INTO administracao.acao
--            (cod_acao
--          , cod_funcionalidade
--          , nom_arquivo
--          , parametro
--          , ordem
--          , complemento_acao
--          , nom_acao)
--     VALUES (2234
--          , 326
--          , 'FLManterEdital.php'
--          , 'imprimir'
--          , 7
--          , ''
--          , 'Reemitir Edital');

update administracao.acao set nom_acao = 'Emitir Autorização de Empenho'  where cod_acao = 1730 ;
update administracao.acao set nom_acao = 'Emitir Autorização de Empenho'  where cod_acao = 1741 ;


INSERT INTO administracao.relatorio (cod_gestao,cod_modulo,cod_relatorio,nom_relatorio,arquivo) values (3,29,8,'Nota de Entrada','notaEntrada.rptdesign');


---------------
-- Ticket #12748
---------------

INSERT INTO administracao.configuracao
            (exercicio
          , cod_modulo
          , parametro
          , valor)
     VALUES (2008
          , 6
          , 'placa_alfanumerica'
          , 'false');


---------------
-- Ticket #12737
---------------

--ALTER TABLE frota.autorizacao ADD COLUMN cgm_motorista INTEGER;

ALTER TABLE frota.autorizacao ADD COLUMN cgm_motorista INTEGER;
ALTER TABLE ONLY frota.autorizacao
    ADD CONSTRAINT fk_autorizacao_6 FOREIGN KEY (cgm_motorista) REFERENCES public.sw_cgm(numcgm);

UPDATE frota.autorizacao
   SET cgm_motorista = cgm_resp_autorizacao;
ALTER TABLE frota.autorizacao ALTER COLUMN cgm_motorista SET NOT NULL;


insert into compras.cotacao_anulada (cod_cotacao,exercicio,motivo) 
select mc.cod_cotacao,mc.exercicio_cotacao,'Anulação da Compra Direta '||cd.cod_compra_direta||'/'||cd.exercicio_entidade||' da Entidade '||cd.cod_entidade  from compras.compra_direta cd 
inner join 
      compras.compra_direta_anulacao cda 
on    cd.cod_compra_direta = cda.cod_compra_direta 
  and cd.cod_modalidade=cda.cod_modalidade 
  and cd.exercicio_entidade = cda.exercicio_entidade 
  and cd.cod_entidade=cda.cod_entidade 
inner join
      compras.mapa_cotacao mc
on
      cd.cod_mapa = mc.cod_mapa 
  and cd.exercicio_mapa = mc.exercicio_mapa
left join 
      compras.cotacao_anulada ca
on
      mc.cod_cotacao       = ca.cod_cotacao
  and mc.exercicio_cotacao = ca.exercicio
where
  ca.exercicio is null
group by 
    cd.cod_entidade,cd.cod_compra_direta,cd.exercicio_entidade,mc.cod_cotacao,mc.exercicio_cotacao;


insert into compras.cotacao_anulada (cod_cotacao,exercicio,motivo) 
select mc.cod_cotacao,mc.exercicio_cotacao,'Anulação da Licitação '||ll.cod_licitacao||'/'||ll.exercicio||' da Entidade '||ll.cod_entidade  from licitacao.licitacao ll
inner join 
      licitacao.licitacao_anulada lla 
on    ll.cod_licitacao = lla.cod_licitacao 
  and ll.cod_modalidade=lla.cod_modalidade 
  and ll.exercicio = lla.exercicio 
  and ll.cod_entidade=lla.cod_entidade 
inner join
      compras.mapa_cotacao mc
on
      ll.cod_mapa = mc.cod_mapa 
  and ll.exercicio_mapa = mc.exercicio_mapa
left join 
      compras.cotacao_anulada ca
on
      mc.cod_cotacao       = ca.cod_cotacao
  and mc.exercicio_cotacao = ca.exercicio
where
  ca.exercicio is null
group by 
    ll.cod_entidade,ll.cod_licitacao,ll.exercicio,mc.cod_cotacao,mc.exercicio_cotacao;

