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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: CalculoTributario.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Função Abstrata de Calculo para a Gestão Tributaria
* Recebe o registro chave e envia para função especializadas
*
* Casos d uso: uc-05.03.05
*/

/*
$Log$
Revision 1.5  2007/02/16 09:56:06  dibueno
Bug #8441#

Revision 1.4  2006/10/24 12:34:57  fabio
correção da TAG de caso de uso

Revision 1.3  2006/09/26 15:20:40  domluc
Ajustes na captura de exception

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION CalculoTributario( integer , varchar , varchar , varchar , integer ) RETURNS boolean AS $$
DECLARE
    -- parametros
    inRegistro  alias for $1;
    stExercicio alias for $2;
    stGrupo     alias for $3;
    stCredito   alias for $4;
    inCodModulo alias for $5;

    -- variaveis
    inExercicio     integer;
    inCodGrupo      integer;
    inCodCredito    integer;
    inCodEspecie    integer;
    inCodGenero     integer;
    inCodNatureza   integer;
    inSandVar       integer;
    boRetorno       boolean;
    stSql           varchar;
    stChave         varchar;
    reRecord        record;
    stTabela        varchar;
    stErro          varchar;
    boErro          boolean;
BEGIN
    -- bloquear tabelas por causa dos inserts
    LOCK TABLE arrecadacao.calculo                      IN EXCLUSIVE MODE;
    LOCK TABLE arrecadacao.calculo_cgm                  IN EXCLUSIVE MODE;
    LOCK TABLE arrecadacao.imovel_calculo               IN EXCLUSIVE MODE;
    LOCK TABLE arrecadacao.cadastro_economico_calculo   IN EXCLUSIVE MODE;
    LOCK TABLE arrecadacao.log_calculo                  IN EXCLUSIVE MODE;
    LOCK TABLE arrecadacao.calculo_grupo_credito        IN EXCLUSIVE MODE;

    -- criar buffer do registro atual
    inSandVar :=  criarBufferInteiro ( 'inRegistro' , inRegistro  ) ;
    inSandVar :=  criarBufferInteiro ( 'inCodModulo', inCodModulo ) ;

    -- cria tabelas temporarias para os calculos e erros
    SELECT tablename
      INTO stTabela
      FROM pg_tables
     WHERE tablename = 'calculos_mensagem';
    IF ( stTabela IS NULL ) THEN
        CREATE TEMP TABLE calculos_mensagem ( cod_calculo int , mensagem varchar );
    ELSE
        DELETE FROM calculos_mensagem;
    END IF;


    SELECT tablename
      INTO stTabela
      FROM pg_tables
     WHERE tablename = 'calculos_correntes';
    IF ( stTabela IS NULL ) THEN
        CREATE TEMP TABLE calculos_correntes ( cod_calculo int , valor numeric);
    ELSE
        DELETE FROM calculos_correntes;
    END IF;

    SELECT tablename
      INTO stTabela
      FROM pg_tables
     WHERE tablename = 'calculos_erro';
    IF ( stTabela IS NULL ) THEN
        CREATE TEMP TABLE calculos_erro ( registro int , credito varchar, funcao varchar , erro boolean, valor numeric);
    ELSE
        DELETE FROM calculos_erro;
    END IF;

    boErro := removerbuffertexto('sterro');

    -- criar buffers
    inExercicio     :=  criarBufferInteiro ( 'inExercicio' , stExercicio::int );
    if ( stGrupo <> '' ) then
        inCodGrupo      :=  criarBufferInteiro ( 'inCodGrupo' , stGrupo::int );
    elsif ( stCredito <> '' ) then
        inCodGrupo      :=  criarBufferInteiro ( 'inCodGrupo' , 0);
        inCodCredito    :=  criarBufferInteiro ( 'inCodCredito' , split_part( stCredito ,'.' , 1 )::int );
        inCodEspecie    :=  criarBufferInteiro ( 'inCodEspecie' , split_part( stCredito ,'.' , 2 )::int );
        inCodGenero     :=  criarBufferInteiro ( 'inCodGenero'  , split_part( stCredito ,'.' , 3 )::int );
        inCodNatureza   :=  criarBufferInteiro ( 'inCodNatureza', split_part( stCredito ,'.' , 4 )::int );
    end if;

    -- direcionar de acordo com o modulo

    if ( inCodModulo = 12 ) then
        stChave := ' Inscricação Imobiliaria ';
        boRetorno := CalculoImobiliario ( );
    if boRetorno IS NULL THEN
       stErro := getErro('CalculoImobiliario');
    end if;
    elsif ( inCodModulo = 14) then
        stChave := ' Inscricação Economica ';
        boRetorno := CalculoEconomico ( );
    if boRetorno IS NULL THEN
       stErro := getErro('CalculoEconomico');
    end if;
    else
        stChave := 'CGM';
        boRetorno := CalculoCgm ( );
    end if;

    if ( stGrupo <> '' ) then
        for reRecord in execute ' select * from calculos_correntes' loop
            -- calculo grupo
            INSERT INTO arrecadacao.calculo_grupo_credito VALUES ( reRecord.cod_calculo , inCodGrupo , inExercicio );
        end loop;
    end if;

    return boRetorno;

END;
$$
LANGUAGE 'plpgsql';
